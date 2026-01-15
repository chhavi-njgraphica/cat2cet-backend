<?php
// require_once __DIR__ . '/simple_html_dom.php';
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;
function getXatStudentResult($url) {
    $data = fetchXatMarks($url);
    $sections_marks = [];
    $obtain_marks = 0;
    $total_marks = 0;
    $gk_section_marks = null;

    $penalty_per_question = 0.10;
    $penalty_after = 8;

    foreach ($data['sections'] as $section) {

        $attempt_questions = 0;
        $correct_answers   = 0;
        $wrong_answers     = 0;
        $unattempted       = 0;
        $_total_marks      = count($section['questions']);
        $_obtain_marks     = 0;
        $attempted = ['Answered', 'Marked For Review'];

        // For each question
        foreach ($section['questions'] as $question) {

            if (in_array($question['attempted'] ?? null, $attempted)) {
                $attempt_questions++;

                if (!empty($question['answered_correctly']) && $question['answered_correctly']) {
                    $correct_answers++;
                    $_obtain_marks += 1;
                } else {
                    $wrong_answers++;
                    // Apply negative for non-GK
                    if ($section['name'] != 'General Knowledge') {
                        $_obtain_marks -= 0.25;
                    }
                }

            } else {
                // unattempted
                $unattempted++;
            }
        }

        // Apply penalty for unattempted questions after first 8
        $extra_unattempted = max(0, $unattempted - $penalty_after);
        $unattempted_penalty = $extra_unattempted * $penalty_per_question;
        $_obtain_marks -= $unattempted_penalty;

        $section_result = [
            'name'                => $section['name'],
            'total_questions'     => $_total_marks,
            'attempt_questions'   => $attempt_questions,
            'correct_answers'     => $correct_answers,
            'wrong_answers'       => $wrong_answers,
            'unattempted'         => $unattempted,
            'unattempted_penalty' => $unattempted_penalty,
            'obtain_marks'        => $_obtain_marks,
        ];

        if ($section['name'] == 'General Knowledge') {
            $gk_section_marks = $section_result;
        } else {
            $sections_marks[] = $section_result;
            $obtain_marks += $_obtain_marks;
            $total_marks  += $_total_marks;
        }
    }
    $percentile_obtain_marks = round($obtain_marks);
    $percentile = @getXatScorePercentile($percentile_obtain_marks);

    return [
        'data'             => $data,
        'percentile' => $percentile,
        'details'          => @$data['details'],
        'sections_marks'   => $sections_marks,
        'gk_section_marks' => $gk_section_marks,
        'obtain_marks'     => $obtain_marks,
        'total_marks'      => $total_marks,
    ];
}



function fetchXatMarks(string $url): array
{
    $client = new Client([ 'http_errors' => false, 'headers' => [ 'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Referer' => 'https://cdn.digialm.com/', ], ]);
    $response = $client->get($url, [ 'allow_redirects' => true, 'verify' => false, ]);
    $html_string = $response->getBody()->getContents();

    // $status      = $response->getStatusCode();
    // $html_string = (string) $response->getBody();

    if ($html_string === '') {
        return ['details' => [], 'sections' => []];
    }

    try {
        $html = HtmlDomParser::str_get_html($html_string);
        // logger()->info('Fetched HTML', ['html' => $html]);
    } catch (\Throwable $e) {
        return ['details' => [], 'sections' => []];
    }

    // --- Student info ---

    $student_info = [];
    foreach ($html->find('div.main-info-pnl table tr') as $tr) {
        $tds = $tr->find('td');
        $student_info[_trim($tds[0]->plaintext, ':')] = _trim($tds[1]->plaintext);
    }

    // --- Sections + questions ---

    $sections_result = [];

    try {
        $sections = $html->find('div.grp-cntnr div.section-cntnr') ?? [];

        foreach ($sections as $section) {
            if (!method_exists($section, 'find')) {
                logger()->warning('Invalid section node skipped', [
                    'type' => get_class($section)
                ]);
                continue;
            }

            $labelNode = $section->find('div.section-lbl', 0);
            $section_name = $labelNode
                ? _trim(str_replace('Section : ', '', _trim($labelNode->plaintext)))
                : 'Section';

            $questions_result = [];
            foreach ($section->find('div.question-pnl') ?? [] as $question) {
                if (method_exists($question, 'find')) {
                    $parsed = parseXatQuestion($question);
                    if ($parsed) {
                        $questions_result[] = $parsed;
                    }
                } else {
                    logger()->warning('Invalid question node detected');
                }
            }

            $sections_result[] = [
                'name'      => $section_name,
                'questions' => $questions_result,
            ];
        }
    } catch (\Throwable $e) {
        logger()->error('DOM method error while parsing sections/questions', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    return [
        'details'  => $student_info,
        'sections' => $sections_result,
    ];
}


function parseXatQuestion($question)
{
    $texts = [];

    foreach ($question->find('td') as $tk => $td) {
        $texts[$tk] = trim(_trim($td->plaintext));
    }

    // logger()->info('Parsed TD texts:', $texts);

    $questionId = null;

    foreach ($texts as $index => $value) {

        // logger()->info("index => $index | value => $value");

        if (stripos($value, 'question id') !== false) {

            //Case 1: "Question ID :993585899" in same string
            if (preg_match('/question\s*id\s*:?\s*(\d+)/i', $value, $matches)) {
                $questionId = $matches[1];
                break;
            }

            //Case 2: ID is in NEXT cell
            // if (isset($texts[$index + 1]) && is_numeric($texts[$index + 1])) {
            //     $questionId = trim($texts[$index + 1]);
            //     break;
            // }

            // //Case 3: ID is in PREVIOUS cell (older format)
            // if (isset($texts[$index - 1]) && is_numeric($texts[$index - 1])) {
            //     $questionId = trim($texts[$index - 1]);
            //     break;
            // }
        }
    }

    if ($questionId !== null) {
        // logger()->info('Question ID extracted => ' . $questionId);
        // logger()->info('Question => ' . $question);
        return parseXatMCQ_Question($question, $questionId, $texts);
    }

    // logger()->warning('Question ID not found');
    return null;
}



function parseXatMCQ_Question($question, $questionId, $texts) {

    static $qaAnswerKey = null;
    static $chosen_option = null;

    // fetch json correct answer data
    if ($qaAnswerKey === null) {
        $qaAnswerKeyPath = dirname(__DIR__) . '/answer-key.json';
        if (file_exists($qaAnswerKeyPath)) {
            $qaAnswerKey = json_decode(file_get_contents($qaAnswerKeyPath), true);
        } else {
            $qaAnswerKey = [];
        }
    }

    //find chosen option
    foreach ($question->find('td') as $td) {
        // Check label
        if (_trim($td->plaintext) === 'Chosen Option :') {
            if ($td->next_sibling()) {
                $chosenOption = trim($td->next_sibling()->plaintext);
            }
            break;
        }
    }

    // fetch correct answer
    $correctAnswer = null;
    if (isset($qaAnswerKey['sections']) && is_array($qaAnswerKey['sections'])) {

        foreach ($qaAnswerKey['sections'] as $sectionName => $questions) {
            if (isset($questions[$questionId])) {
                $correctAnswer = $questions[$questionId];

                // logger()->info("Found in section => " . $sectionName);
                break;
            }
        }
    }

    //fetch status value
    $status = null;
    foreach ($question->find('td') as $td) {
        // Check label
        if (_trim($td->plaintext) === 'Status :') {
            if ($td->next_sibling()) {
                $status = trim($td->next_sibling()->plaintext);
                // logger()->info("Status => " . $status);  
            }
            break;
        }
    }
    // foreach ($texts as $index => $value) {
    //      logger()->info("text => " . $value);  
    // }

    // options fetch it is image or text  
   $table = $question->find('.questionRowTbl', 0);

    $options = [];

    if ($table) {

        $rows = $table->find('tr');

        foreach ($rows as $tr) {

            $tds = $tr->find('td');
            if (count($tds) < 2) continue;

            $td = $tds[1];

            $textOnly = trim($td->text);
            $html     = trim($td->innerhtml);

            if (preg_match('/^([A-Z])\.\s*/', $textOnly, $m)) {

                $letter = $m[1];
                // Find image
                $img = $td->findOne('img');
                $imageName = $img ? $img->getAttribute('name') : null;

                $options[] = [
                    "letter" => $letter,
                    "text"   => $textOnly,
                    "html"   => $html,
                    "image"  => $imageName,
                ];

            } 
        }
    }



    $given_answer_text = null;
    $given_answer_image = null;

    foreach ($options as $opt) {

        if (!isset($opt['letter'])) continue; // <— FIX

        if ($opt['letter'] === $chosenOption) {

            $given_answer_text  = $opt['text'];
            $given_answer_image = $opt['image'];

            // logger()->info("Given Answer Text => " . $given_answer_text);
            // logger()->info("Given Answer Image => " . $given_answer_image);

            break;
        }
    }
 
   
    // fetch correct answer
    $answered_correctly = false;

    if ($correctAnswer !== null) {

        // Image answer
        if (!empty($given_answer_image)) {
            if (trim($correctAnswer) === trim($given_answer_image)) {
                $answered_correctly = true;
            }
        }

        // Text answer
        else if (!empty($given_answer_text)) {
            // Normalize: remove "A. " label
            $cleanGiven = preg_replace('/^[A-Z]\.\s*/', '', $given_answer_text);
            $cleanCorrect = trim($correctAnswer);

            if ($cleanGiven === $cleanCorrect) {
                $answered_correctly = true;
            }
        }
    }

    // logger()->info("Chosen Option => " . $chosenOption);
    // logger()->info("Correct Answer => " . $correctAnswer); 
    // logger()->info("Answer Correctly => " . $answered_correctly); 
   
    $attemptedStatus = match($status) {
        'Answered', 'Answered and Marked For Review' => 'Answered',
        'Marked For Review' => 'Marked For Review',
        default => null
    };
    // logger()->info("Attempted Status => " . $attemptedStatus); 
    return [
        'id' => $questionId,
        'attempted' => $attemptedStatus,
        'correct_answer' => $correctAnswer,
        'given_answer' => $chosenOption,
        'answered_correctly' => $answered_correctly,
    ];

}

// function parseSA_Question($question) {
//     // Load QA SA answer key
//     static $qaAnswerKey = null;
//     if ($qaAnswerKey === null) {
//         $qaAnswerKeyPath = dirname(__DIR__) . '/qa-sa-answer-key.json';
//         if (file_exists($qaAnswerKeyPath)) {
//             $answerKeyJson = file_get_contents($qaAnswerKeyPath);
//             $answerKeyData = json_decode($answerKeyJson, true);
//             // Use + operator to preserve string keys
//             $qaAnswerKey = $answerKeyData['slots']['slot1'] +
//                            $answerKeyData['slots']['slot2'] +
//                            $answerKeyData['slots']['slot3'];
//         } else {
//             $qaAnswerKey = [];
//         }
//     }

//     $texts = [];
//     foreach ($question->find('td') as $tk => $td){
//         $texts[$tk] = _t($td->plaintext);
//     }
//     /**
//      * Check SA question sub type is 'Comprehension passage'
//      */
//     if(strpos(strtolower(@$texts[2]), 'comprehension') !== false) {
//         $rtexts = array_reverse($texts);
//         // logger()->info($rtexts);
//         $correct_answer = _t(str_replace('possible answer:', '', strtolower(@$rtexts[8])));
//         $given_answer = _t(str_replace('given answer :', '', strtolower(@$rtexts[6])));
//         $question_id = @$rtexts[2];

//         // Check if this is a QA SA question (correct answer is "na")
//         if ($correct_answer === 'na' && isset($qaAnswerKey[$question_id])) {
//             $correct_answer = $qaAnswerKey[$question_id];
//         }

//         $answered_correctly = ($correct_answer !== null && $given_answer !== null && $correct_answer == $given_answer);

//         return [
//             'passage_title' => @$texts[23],
//             'passage' => @$texts[21],
//             'parse_as' => 'sa',
//             'sub_type' => 'comprehension',
//             'type' => strtolower(@$rtexts[4]), // SA
//             'cs' => _t(str_replace('case sensitivity:', '', strtolower(@$rtexts[12]))),
//             'id' => $question_id,
//             'number' => @$rtexts[16],
//             'name' => @$rtexts[15],
//             'attempted' => isQuestionAttempted(@$rtexts[0]),
//             'correct_answer' => $correct_answer,
//             'given_answer' => $given_answer,
//             'answered_correctly' => $answered_correctly,
//         ];
//     }
//     else {
//         $correct_answer = _t(str_replace('possible answer:', '', strtolower(@$texts[9])));
//         $given_answer = @$texts[11];
//         $question_id = @$texts[15];

//         // Check if this is a QA SA question (correct answer is "na")
//         if ($correct_answer === 'na' && isset($qaAnswerKey[$question_id])) {
//             $correct_answer = $qaAnswerKey[$question_id];
//         }

//         $answered_correctly = ($correct_answer !== null && $given_answer !== null && $correct_answer == $given_answer);

//         // foreach ($texts as $index => $value) {
//         //     logger()->info("Index: $index", ['value' => $value]);
//         // }
//         return [
//             'parse_as' => 'sa',
//             'sub_type' => 'jumbled',
//             'type' => strtolower(@$texts[13]), // SA
//             'cs' => _t(str_replace('case sensitivity:', '', strtolower(@$texts[5]))),
//             'id' => $question_id,
//             'number' => @$texts[1],
//             'name' => @$texts[2],
//             'attempted' => isQuestionAttempted(@$texts[17]),
//             'correct_answer' => $correct_answer,
//             'given_answer' => $given_answer,
//             'answered_correctly' => $answered_correctly,
//         ];
//     }
// }
// function isQuestionAttempted($status){
//     $status = _t(strtolower($status));

//     return in_array($status,["Answered"]);
// }


// function getXatScorePercentile($score)
// {
//     $pp = "50%tile";

//     if ($score >= 37) {
//         $pp = "99%tile";
//     } else if ($score >= 33) {
//         $pp = "98%tile";
//     } else if ($score == 32) {
//         $pp = "97%tile";
//     } else if ($score == 31) {
//         $pp = "96%tile";
//     } else if ($score >= 29 && $score <= 30) {
//         $pp = "95%tile";
//     } else if ($score >= 27 && $score <= 28) {
//         $pp = "90%tile";     
//     }else if ($score == 26) {
//         $pp = "88%tile";  
//     }else if ($score == 25) {
//         $pp = "87%tile";   
//     }else if ($score == 24) {
//         $pp = "86%tile"; 
//     } else if ($score >= 22 && $score <= 23) {
//         $pp = "85%tile";
//     }else if ($score == 21) {
//         $pp = "82%tile"; 
//     } else if ($score >= 19 && $score <= 20) {
//         $pp = "80%tile";
//     } else if ($score == 18) {
//         $pp = "75%tile";
//     } else if ($score == 17) {
//         $pp = "70%tile";
//     } else if ($score >= 15 && $score <= 16) {
//         $pp = "65%tile";
//     } else if ($score == 14) {
//         $pp = "60%tile";
//     }

//     return $pp;
// }


function getXatScorePercentile($score)
{
    $score = (int) floor($score);
    if ($score >= 51) return "99.96%tile";
    if ($score == 50) return "99.96%tile";
    if ($score == 49) return "99.95%tile";
    if ($score == 48) return "99.93%tile";
    if ($score == 47) return "99.92%tile";
    if ($score == 46) return "99.91%tile";
    if ($score == 45) return "99.90%tile";
    if ($score == 44) return "99.86%tile";
    if ($score == 43) return "99.80%tile";
    if ($score == 42) return "99.72%tile";
    if ($score == 41) return "99.60%tile";
    if ($score == 40) return "99.35%tile";
    if ($score == 39) return "99.25%tile";
    if ($score == 38) return "99.15%tile";
    if ($score == 37) return "99%tile";
    if ($score == 36) return "98.70%tile";
    if ($score == 35) return "98.30%tile";
    if ($score == 34) return "97.90%tile";
    if ($score == 33) return "97.50%tile";
    if ($score == 32) return "97%tile";
    if ($score == 31) return "96.30%tile";
    if ($score == 30) return "95.50%tile";
    if ($score == 29) return "94.70%tile";
    if ($score == 28) return "93.80%tile";
    if ($score == 27) return "92.80%tile";
    if ($score == 26) return "91.60%tile";
    if ($score == 25) return "90.40%tile";
    if ($score == 24) return "88.80%tile";
    if ($score == 23) return "87%tile";
    if ($score == 22) return "85%tile";
    if ($score == 21) return "83%tile";
    if ($score == 20) return "81%tile";
    if ($score == 19) return "79.20%tile";
    if ($score == 18) return "77%tile";
    if ($score == 17) return "74.50%tile";
    if ($score == 16) return "71.50%tile";
    if ($score == 15) return "68.50%tile";
    return "50%tile";
}



function sendXatJson($data, $http_code = 200) {
    header('Content-type: application/json');
    http_response_code($http_code);
    echo json_encode($data);
    die;
}

function _trim($str, $remove_extra = '', $remove_extra_by = '') {
    $str = str_replace('&nbsp;', ' ', $str);
    $str = str_replace($remove_extra, $remove_extra_by, $str);
    $str = html_entity_decode($str, ENT_QUOTES | ENT_COMPAT , 'UTF-8');
    $str = html_entity_decode($str, ENT_HTML5, 'UTF-8');
    $str = html_entity_decode($str);
    $str = htmlspecialchars_decode($str);
    $str = strip_tags($str);
    return trim($str);
}
