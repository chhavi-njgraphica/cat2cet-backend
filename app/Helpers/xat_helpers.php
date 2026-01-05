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

    $percentile = @getXatScorePercentile($obtain_marks);

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


function getXatScorePercentile($score){
    $pp = "0%tile - 10%tile";
    
    if ($score > 132 && $score < 204) {
            $pp = "100%tile";
    }else if ($score == 131) {
        $pp = "99.99%tile";
    }else if ($score == 130) {
        $pp = "99.98%tile";
    } else if ($score == 129) {
        $pp = "99.97%tile";
    } else if ($score == 128) {
        $pp = "99.96%tile";
    } else if ($score == 127) {
        $pp = "99.96%tile";
    } else if ($score == 126) {
        $pp = "99.95%tile";
    } else if ($score == 125) {
        $pp = "99.94%tile";
    } else if ($score == 124) {
        $pp = "99.93%tile";
    } else if ($score == 123 || $score == 122) {
        $pp = "99.92%tile";
    } else if ($score == 121) {
        $pp = "99.91%tile";
    } else if ($score == 120) {
        $pp = "99.90%tile";
    } else if ($score == 119) {
        $pp = "99.98%tile";
    } else if ($score == 118) {
        $pp = "99.85%tile";
    } else if ($score == 117) {
        $pp = "99.83%tile";
    } else if ($score == 116) {
        $pp = "99.80%tile";
    } else if ($score == 115) {
        $pp = "99.78%tile";
    } else if ($score == 114) {
        $pp = "99.75%tile";
    } else if ($score == 113) {
        $pp = "99.73%tile";
    } else if ($score == 112) {
        $pp = "99.70%tile";
    } else if ($score == 111) {
        $pp = "99.68%tile";
    } else if ($score == 110) {
        $pp = "99.65%tile";
    } else if ($score == 109) {
        $pp = "99.62%tile";
    } else if ($score == 108) {
        $pp = "99.60%tile";
    } else if ($score == 107) {
        $pp = "99.57%tile";
    } else if ($score == 106) {
        $pp = "99.55%tile";
    } else if ($score == 105) {
        $pp = "99.52%tile";
    } else if ($score == 104) {
        $pp = "99.50%tile";
    } else if ($score == 103) {
        $pp = "99.47%tile";
    } else if ($score == 102) {
        $pp = "99.44%tile";
    } else if ($score == 101) {
        $pp = "99.41%tile";
    } else if ($score == 100) {
        $pp = "99.38%tile";
    } else if ($score == 99) {
        $pp = "99.35%tile";
    } else if ($score == 98) {
        $pp = "99.32%tile";
    } else if ($score == 97) {
        $pp = "99.29%tile";
    } else if ($score == 96) {
        $pp = "99.26%tile";
    } else if ($score == 95) {
        $pp = "99.23%tile";
    }else if ($score == 94) {
        $pp = "99.20%tile";
    }else if ($score == 93) {
        $pp = "99.17%tile";
    }else if ($score == 92) {
        $pp = "99.14%tile";
    }else if ($score == 91) {
        $pp = "99.11%tile";
    }else if ($score == 90) {
        $pp = "99.08%tile";
    }else if ($score == 89) {
        $pp = "99.05%tile";
    }else if ($score == 88) {
        $pp = "99.02%tile";
    } else if ($score == 87) {
        $pp = "98.99%tile";
    } else if ($score == 86) {
        $pp = "98.84%tile";
    } else if ($score == 85) {
        $pp = "98.69%tile";
    } else if ($score == 84) {
        $pp = "98.54%tile";
    } else if ($score == 83) {
        $pp = "99.39%tile";
    } else if ($score == 82) {
        $pp = "98.24%tile";
    } else if ($score == 81) {
        $pp = "99.09%tile";
    } else if ($score == 80) {
        $pp = "97.94%tile";
    } else if ($score == 79) {
        $pp = "97.64%tile";
    } else if ($score == 78) {
        $pp = "97.34%tile";
    } else if ($score == 77) {
        $pp = "97.04%tile";
    } else if ($score == 76) {
        $pp = "96.74%tile";
    } else if ($score == 75) {
        $pp = "96.44%tile";
    } else if ($score == 74) {
        $pp = "96.14%tile";
    } else if ($score == 73) {
        $pp = "95.84%tile";
    } else if ($score == 72) {
        $pp = "95.54%tile";
    } else if ($score == 71) {
        $pp = "95.24%tile";
    } else if ($score == 70) {
        $pp = "94.94%tile";
    } else if ($score == 69) {
        $pp = "94.69%tile";
    } else if ($score == 68) {
        $pp = "94.44%tile";
    } else if ($score == 67) {
        $pp = "94.19%tile";
    } else if ($score == 66) {
        $pp = "93.94%tile";
    } else if ($score == 65) {
        $pp = "93.69%tile";
    } else if ($score == 64) {
        $pp = "93.44%tile";
    } else if ($score == 63) {
        $pp = "93.19%tile";
    } else if ($score == 62) {
        $pp = "92.94%tile";
    } else if ($score == 61) {
        $pp = "92.69%tile";
    } else if ($score == 60) {
        $pp = "92.44%tile";
    } else if ($score == 59) {
        $pp = "92.19%tile";
    } else if ($score == 58) {
        $pp = "91.94%tile";
    } else if ($score == 57) {
        $pp = "91.69%tile";
    } else if ($score == 56) {
        $pp = "91.44%tile";
    } else if ($score == 55) {
        $pp = "91.19%tile";
    } else if ($score == 54) {
        $pp = "90.94%tile";
    } else if ($score == 53) {
        $pp = "90.69%tile";
    } else if ($score == 52) {
        $pp = "90.44%tile";
    } else if ($score == 51) {
        $pp = "89.64%tile";
    } else if ($score == 50) {
        $pp = "88.84%tile";
    } else if ($score == 49) {
        $pp = "88.04%tile";
    } else if ($score == 48) {
        $pp = "87.24%tile";
    } else if ($score == 47) {
        $pp = "86.44%tile";
    } else if ($score == 46) {
        $pp = "85.64%tile";
    } else if ($score == 45) {
        $pp = "85.00%tile";
    } else if ($score == 44) {
        $pp = "84.02%tile";
    } else if ($score == 43) {
        $pp = "83.04%tile";
    } else if ($score == 42) {
        $pp = "82.06%tile";
    } else if ($score == 41) {
        $pp = "81.08%tile";
    } else if ($score == 40) {
        $pp = "80.10%tile";
    } else if ($score == 39) {
        $pp = "79.40%tile";
    } else if ($score == 38) {
        $pp = "78.70%tile";
    } else if ($score == 37) {
        $pp = "78.00%tile";
    } else if ($score == 36) {
        $pp = "77.30%tile";
    } else if ($score == 35) {
        $pp = "76.60%tile";
    } else if ($score == 34) {
        $pp = "75.90%tile";
    } else if ($score == 33) {
        $pp = "75.20%tile";
    } else if ($score == 32) {
        $pp = "74.50%tile";
    } else if ($score == 31) {
        $pp = "72.90%tile";
    } else if ($score == 30) {
        $pp = "71.30%tile";
    } else if ($score == 29) {
        $pp = "69.70%tile";
    } else if ($score == 28) {
        $pp = "68.10%tile";
    } else if ($score == 27) {
        $pp = "66.50%tile";
    } else if ($score == 26) {
        $pp = "64.90%tile";
    } else if ($score == 25) {
        $pp = "63.30%tile";
    } else if ($score == 24) {
        $pp = "61.70%tile";
    } else if ($score == 23) {
        $pp = "60.10%tile";
    } else if ($score == 22) {
        $pp = "58.50%tile";
    } else if ($score == 21) {
        $pp = "56.90%tile";
    } else if ($score == 20) {
        $pp = "55.30%tile";
    } else if ($score == 19) {
        $pp = "53.70%tile";
    } else if ($score == 18) {
        $pp = "52.10%tile";
    } else if ($score == 17) {
        $pp = "50.50%tile";
    } else if ($score == 16) {
        $pp = "48.90%tile";
    } else if ($score == 15) {
        $pp = "47.30%tile";
    } else if ($score == 14) {
        $pp = "45.70%tile";
    } else if ($score == 13) {
        $pp = "44.10%tile";
    } else if ($score == 12) {
        $pp = "42.50%tile";
    } else if ($score == 11) {
        $pp = "40.90%tile";
    } else if ($score == 10) {
        $pp = "39.30%tile";
    } else if ($score == 9) {
        $pp = "37.70%tile";
    } else if ($score == 8) {
        $pp = "36.10%tile";
    } else if ($score == 7) {
        $pp = "34.50%tile";
    } else if ($score == 6) {
        $pp = "32.90%tile";
    } else if ($score == 5) {
        $pp = "31.30%tile";
    } else if ($score == 4) {
        $pp = "29.70%tile";
    } else if ($score == 3) {
        $pp = "28.10%tile";
    } else if ($score == 2) {
        $pp = "26.50%tile";
    } else if ($score == 1) {
        $pp = "24.90%tile";
    } else if ($score == 0) {
        $pp = "23.30%tile";
    } else if ($score >= -68 && $score <= -1) {
            $pp = "0%tile";
    }
    return $pp;
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
