<?php
// require_once __DIR__ . '/simple_html_dom.php';
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;
function getStudentResult($url) {
    $data = fetchMarks($url);
    $sections_marks = [];
    $obtain_marks = 0;
    $total_marks = 0;
    foreach (@$data['sections'] as $section) {
        $attempt_questions = 0;
        $correct_answers = 0;
        $wrong_answers = 0;
        $_total_marks = 0;
        $_obtain_marks = 0;
        foreach ($section['questions'] as $k => $question) {
            $_total_marks += 3;
            // $section['questions'][$k]['marks'] = 0;
            if(@$question['attempted']){
                $attempt_questions++;
                if(@$question['answered_correctly']){
                    $correct_answers++;
                    $_obtain_marks += 3;
                    // $section['questions'][$k]['marks'] = 3;
                }else{
                    $wrong_answers++;
                    if(@$question['type'] == 'mcq'){
                        $_obtain_marks -= 1;
                        // $section['questions'][$k]['marks'] = -1;
                    }
                }
            }
        }
        //$_obtain_marks = $_obtain_marks > 0 ? $_obtain_marks: 0;
        $sections_marks[] = [
            'name' => @$section['name'],
            // 'questions' => @$section['questions'],
            'total_questions' => count(@$section['questions']),
            'attempt_questions' => $attempt_questions,
            'correct_answers' => $correct_answers,
            'wrong_answers' => $wrong_answers,
            'obtain_marks' => $_obtain_marks,
            'total_marks' => $_total_marks,
        ];
        $obtain_marks += $_obtain_marks;
        $total_marks += $_total_marks;
    }

    $shift = 1;

    switch(@$data['details']['Test Time']){
        case "8:30 AM - 10:30 AM":
            $shift = 1;
            break;
        case "12:30 PM - 2:30 PM":
            $shift = 2;
            break;
        case "4:30 PM - 6:30 PM":
            $shift = 3;
            break;
    }

    @$data['details']['Shift'] = $shift;

    $percentile = @getScorePercentile($obtain_marks, $shift);

    return [
        //'data' => $data,
        'percentile' => $percentile,
        'details' => @$data['details'],
        'sections_marks' => $sections_marks,
        'obtain_marks' => $obtain_marks,
        'total_marks' => $total_marks,
    ];
}


function fetchMarks(string $url): array
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
        $student_info[_t($tds[0]->plaintext, ':')] = _t($tds[1]->plaintext);
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
                ? _t(str_replace('Section : ', '', _t($labelNode->plaintext)))
                : 'Section';

            $questions_result = [];
            foreach ($section->find('div.question-pnl') ?? [] as $question) {
                if (method_exists($question, 'find')) {
                    $parsed = parseQuestion($question);
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

    // logger()->info('Fetched HTML', ['details' => $student_info, 'sections' => $sections_result]);

    return [
        'details'  => $student_info,
        'sections' => $sections_result,
    ];
}


function parseQuestion($question) {
    $texts = [];
    foreach ($question->find('td') as $tk => $td){
        $texts[$tk] =_t($td->plaintext);
    }
    $rtexts = array_reverse($texts);
    if(in_array('mcq', [strtolower(@$texts[13]), strtolower(@$texts[21]), strtolower(@$rtexts[6]), strtolower(@$rtexts[14])])){
        return parseMCQ_Question($question);
    }elseif(in_array('sa', [strtolower(@$rtexts[4])])){
        return parseSA_Question($question);
    }else{
        // return $texts;
    }
}

function parseMCQ_Question($question) {
    $texts = [];
    $correct_option = 0;
    $correct_option_index = 0;
    foreach ($question->find('td') as $tk => $td) {
        // Get the text content
        $texts[$tk] = _t($td->plaintext);
    

        $class = $td->getAttribute('class'); // safer than $td->class
        if ($class && stripos($class, 'rightans') !== false) {
            $correct_option_index = $tk;
        }
    }
    $rev_correct_option_index = (count($texts) - $correct_option_index) - 1;

    /**
     * Check MSQ question sub type is 'Comprehension passage'
     */
    if(strpos(strtolower(@$texts[2]), 'comprehension') !== false){

        // Extract correct option number from the cell text
        $correct_option = explode('.', @$texts[$correct_option_index])[0];

        $rtexts = array_reverse($texts);
        // logger()->info($rtexts); //for check indexing



        return [
            'passage_title' => @$texts[25],
            'passage' => @$texts[27],
            'parse_as' => 'mcq',
            'sub_type' => 'comprehension',
            'type' => strtolower(@$rtexts[array_search('MCQ', $rtexts)] ?? ''), // MCQ
            'id' => @$rtexts[4],
            'number' => @$rtexts[8],
            'name' => @$rtexts[17],
            'options' => [
                1 => @$rtexts[14],
                2 => @$rtexts[12],
                3 => @$rtexts[10],
                4 => @$rtexts[8],
            ],
            'attempted' => isQuestionAttempted(@$rtexts[2]),
            'correct_option' => $correct_option,
            'selected_option' => @$rtexts[0],
            'answered_correctly' => $correct_option == @$rtexts[0],
        ];


    }
    else{
        // dd($texts, $correct_option_index);
        /* switch ($correct_option_index){
            case 13: case 5: $correct_option = 1; break;
            case 15: case 7: $correct_option = 2; break;
            case 17: case 9: $correct_option = 3; break;
            case 19: case 11: $correct_option = 4; break;
        } */

        // $correct_option = explode(' ', @$texts[$correct_option_index])[2];
        $correct_option = explode('.', @$texts[$correct_option_index])[0];

        // foreach ($texts as $index => $value) {
        //     logger()->info("Index: $index", ['value' => $value]);
        // }  for indexing
        return [
            'parse_as' => 'mcq',
            'sub_type' => 'alternate',
            'type' => strtolower(@$texts[13]), // MCQ
            'id' => @$texts[15],
            'number' => @$texts[1],
            'name' => @$texts[2],
            'options' => [
                1 => @$texts[5],
                2 => @$texts[7],
                3 => @$texts[9],
                4 => @$texts[11],
            ],
            'attempted' => isQuestionAttempted(@$texts[25]),
            'correct_option' => $correct_option,
            'selected_option' => @$texts[27],
            'answered_correctly' => $correct_option == @$texts[27],
        ];
    }
}

function parseSA_Question($question) {
    // Load QA SA answer key
    static $qaAnswerKey = null;
    if ($qaAnswerKey === null) {
        $qaAnswerKeyPath = dirname(__DIR__) . '/qa-sa-answer-key.json';
        if (file_exists($qaAnswerKeyPath)) {
            $answerKeyJson = file_get_contents($qaAnswerKeyPath);
            $answerKeyData = json_decode($answerKeyJson, true);
            // Use + operator to preserve string keys
            $qaAnswerKey = $answerKeyData['slots']['slot1'] +
                           $answerKeyData['slots']['slot2'] +
                           $answerKeyData['slots']['slot3'];
        } else {
            $qaAnswerKey = [];
        }
    }

    $texts = [];
    foreach ($question->find('td') as $tk => $td){
        $texts[$tk] = _t($td->plaintext);
    }
    /**
     * Check SA question sub type is 'Comprehension passage'
     */
    if(strpos(strtolower(@$texts[2]), 'comprehension') !== false) {
        $rtexts = array_reverse($texts);
        // logger()->info($rtexts);
        $correct_answer = _t(str_replace('possible answer:', '', strtolower(@$rtexts[8])));
        $given_answer = _t(str_replace('given answer :', '', strtolower(@$rtexts[6])));
        $question_id = @$rtexts[2];

        // Check if this is a QA SA question (correct answer is "na")
        if ($correct_answer === 'na' && isset($qaAnswerKey[$question_id])) {
            $correct_answer = $qaAnswerKey[$question_id];
        }

        $answered_correctly = ($correct_answer !== null && $given_answer !== null && $correct_answer == $given_answer);

        return [
            'passage_title' => @$texts[23],
            'passage' => @$texts[21],
            'parse_as' => 'sa',
            'sub_type' => 'comprehension',
            'type' => strtolower(@$rtexts[4]), // SA
            'cs' => _t(str_replace('case sensitivity:', '', strtolower(@$rtexts[12]))),
            'id' => $question_id,
            'number' => @$rtexts[16],
            'name' => @$rtexts[15],
            'attempted' => isQuestionAttempted(@$rtexts[0]),
            'correct_answer' => $correct_answer,
            'given_answer' => $given_answer,
            'answered_correctly' => $answered_correctly,
        ];
    }
    else {
        $correct_answer = _t(str_replace('possible answer:', '', strtolower(@$texts[9])));
        $given_answer = @$texts[11];
        $question_id = @$texts[15];

        // Check if this is a QA SA question (correct answer is "na")
        if ($correct_answer === 'na' && isset($qaAnswerKey[$question_id])) {
            $correct_answer = $qaAnswerKey[$question_id];
        }

        $answered_correctly = ($correct_answer !== null && $given_answer !== null && $correct_answer == $given_answer);

        // foreach ($texts as $index => $value) {
        //     logger()->info("Index: $index", ['value' => $value]);
        // }
        return [
            'parse_as' => 'sa',
            'sub_type' => 'jumbled',
            'type' => strtolower(@$texts[13]), // SA
            'cs' => _t(str_replace('case sensitivity:', '', strtolower(@$texts[5]))),
            'id' => $question_id,
            'number' => @$texts[1],
            'name' => @$texts[2],
            'attempted' => isQuestionAttempted(@$texts[17]),
            'correct_answer' => $correct_answer,
            'given_answer' => $given_answer,
            'answered_correctly' => $answered_correctly,
        ];
    }
}
function isQuestionAttempted($status){
    $status = _t(strtolower($status));

    return in_array($status,["answered", "marked for review"]);
}

function getScorePercentile($score, $shift){
    $pp = "0%tile";
    if ($shift == 1) {
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
    }
    else if ($shift == 2) {
        if ($score >= 129 && $score <= 204) {
            $pp = "100%tile";
        } else if ($score == 128) {
            $pp = "99.99%tile";
        } else if ($score == 127) {
            $pp = "99.98%tile";
        } else if ($score == 126) {
            $pp = "99.97%tile";
        } else if ($score == 125 || $score == 124) {
            $pp = "99.96%tile";
        } else if ($score == 123) {
            $pp = "99.95%tile";
        } else if ($score == 122) {
            $pp = "99.94%tile";
        } else if ($score == 121) {
            $pp = "99.93%tile";
        } else if ($score == 120 || $score == 119) {
            $pp = "99.92%tile";
        } else if ($score == 118) {
            $pp = "99.91%tile";
        } else if ($score == 117) {
            $pp = "99.90%tile";
        } else if ($score == 116) {
            $pp = "99.88%tile";
        } else if ($score == 115) {
            $pp = "99.85%tile";
        } else if ($score == 114) {
            $pp = "99.83%tile";
        } else if ($score == 113) {
            $pp = "99.80%tile";
        } else if ($score == 112) {
            $pp = "99.78%tile";
        } else if ($score == 111) {
            $pp = "99.75%tile";
        } else if ($score == 110) {
            $pp = "99.73%tile";
        } else if ($score == 109) {
            $pp = "99.70%tile";
        } else if ($score == 108) {
            $pp = "99.68%tile";
        } else if ($score == 107) {
            $pp = "99.65%tile";
        } else if ($score == 106) {
            $pp = "99.62%tile";
        } else if ($score == 105) {
            $pp = "99.60%tile";
        } else if ($score == 104) {
            $pp = "99.57%tile";
        } else if ($score == 103) {
            $pp = "99.55%tile";
        } else if ($score == 102) {
            $pp = "99.52%tile";
        } else if ($score == 101) {
            $pp = "99.50%tile";
        } else if ($score == 100) {
            $pp = "99.47%tile";
        } else if ($score == 99) {
            $pp = "99.44%tile";
        } else if ($score == 98) {
            $pp = "99.41%tile";
        } else if ($score == 97) {
            $pp = "99.38%tile";
        } else if ($score == 96) {
            $pp = "99.35%tile";
        } else if ($score == 95) {
            $pp = "99.32%tile";
        }else if ($score == 94) {
            $pp = "99.29%tile";
        }else if ($score == 93) {
            $pp = "99.26%tile";
        }else if ($score == 92) {
            $pp = "99.23%tile";
        }else if ($score == 91) {
            $pp = "99.20%tile";
        }else if ($score == 90) {
            $pp = "99.17%tile";
        }else if ($score == 89) {
            $pp = "99.14%tile";
        }else if ($score == 88) {
            $pp = "99.11%tile";
        } else if ($score == 87) {
            $pp = "99.08%tile";
        } else if ($score == 86) {
            $pp = "99.05%tile";
        } else if ($score == 85) {
            $pp = "99.02%tile";
        } else if ($score == 84) {
            $pp = "98.99%tile";
        } else if ($score == 83) {
            $pp = "98.84%tile";
        } else if ($score == 82) {
            $pp = "98.69%tile";
        } else if ($score == 81) {
            $pp = "98.54%tile";
        } else if ($score == 80) {
            $pp = "98.39%tile";
        } else if ($score == 79) {
            $pp = "98.24%tile";
        } else if ($score == 78) {
            $pp = "98.09%tile";
        } else if ($score == 77) {
            $pp = "97.94%tile";
        } else if ($score == 76) {
            $pp = "97.64%tile";
        } else if ($score == 75) {
            $pp = "97.34%tile";
        } else if ($score == 74) {
            $pp = "97.04%tile";
        } else if ($score == 73) {
            $pp = "96.74%tile";
        } else if ($score == 72) {
            $pp = "96.44%tile";
        } else if ($score == 71) {
            $pp = "96.14%tile";
        } else if ($score == 70) {
            $pp = "95.84%tile";
        } else if ($score == 69) {
            $pp = "95.54%tile";
        } else if ($score == 68) {
            $pp = "95.24%tile";
        } else if ($score == 67) {
            $pp = "94.94%tile";
        } else if ($score == 66) {
            $pp = "94.69%tile";
        } else if ($score == 65) {
            $pp = "94.44%tile";
        } else if ($score == 64) {
            $pp = "94.19%tile";
        } else if ($score == 63) {
            $pp = "93.94%tile";
        } else if ($score == 62) {
            $pp = "93.69%tile";
        } else if ($score == 61) {
            $pp = "93.44%tile";
        } else if ($score == 60) {
            $pp = "93.19%tile";
        } else if ($score == 59) {
            $pp = "92.94%tile";
        } else if ($score == 58) {
            $pp = "92.69%tile";
        } else if ($score == 57) {
            $pp = "92.44%tile";
        } else if ($score == 56) {
            $pp = "92.19%tile";
        } else if ($score == 55) {
            $pp = "91.94%tile";
        } else if ($score == 54) {
            $pp = "91.69%tile";
        } else if ($score == 53) {
            $pp = "91.44%tile";
        } else if ($score == 52) {
            $pp = "91.19%tile";
        } else if ($score == 51) {
            $pp = "90.94%tile";
        } else if ($score == 50) {
            $pp = "90.69%tile";
        } else if ($score == 49) {
            $pp = "90.44%tile";
        } else if ($score == 48) {
            $pp = "89.64%tile";
        } else if ($score == 47) {
            $pp = "88.84%tile";
        } else if ($score == 46) {
            $pp = "88.04%tile";
        } else if ($score == 45) {
            $pp = "87.24%tile";
        } else if ($score == 44) {
            $pp = "86.44%tile";
        } else if ($score == 43) {
            $pp = "85.64%tile";
        } else if ($score == 42) {
            $pp = "85.00%tile";
        } else if ($score == 41) {
            $pp = "84.02%tile";
        } else if ($score == 40) {
            $pp = "83.04%tile";
        } else if ($score == 39) {
            $pp = "82.06%tile";
        } else if ($score == 38) {
            $pp = "81.08%tile";
        } else if ($score == 37) {
            $pp = "80.10%tile";
        } else if ($score == 36) {
            $pp = "79.40%tile";
        } else if ($score == 35) {
            $pp = "78.70%tile";
        } else if ($score == 34) {
            $pp = "78.00%tile";
        } else if ($score == 33) {
            $pp = "77.30%tile";
        } else if ($score == 32) {
            $pp = "76.60%tile";
        } else if ($score == 31) {
            $pp = "75.90%tile";
        } else if ($score == 30) {
            $pp = "75.20%tile";
        } else if ($score == 29) {
            $pp = "74.50%tile";
        } else if ($score == 28) {
            $pp = "72.90%tile";
        } else if ($score == 27) {
            $pp = "71.30%tile";
        } else if ($score == 26) {
            $pp = "69.70%tile";
        } else if ($score == 25) {
            $pp = "68.10%tile";
        } else if ($score == 24) {
            $pp = "66.50%tile";
        } else if ($score == 23) {
            $pp = "64.90%tile";
        } else if ($score == 22) {
            $pp = "63.30%tile";
        } else if ($score == 21) {
            $pp = "61.70%tile";
        } else if ($score == 20) {
            $pp = "60.10%tile";
        } else if ($score == 19) {
            $pp = "58.50%tile";
        } else if ($score == 18) {
            $pp = "56.90%tile";
        } else if ($score == 17) {
            $pp = "55.30%tile";
        } else if ($score == 16) {
            $pp = "53.70%tile";
        } else if ($score == 15) {
            $pp = "52.10%tile";
        } else if ($score == 14) {
            $pp = "50.50%tile";
        } else if ($score == 13) {
            $pp = "48.90%tile";
        } else if ($score == 12) {
            $pp = "47.30%tile";
        } else if ($score == 11) {
            $pp = "45.70%tile";
        } else if ($score == 10) {
            $pp = "44.10%tile";
        } else if ($score == 9) {
            $pp = "42.50%tile";
        } else if ($score == 8) {
            $pp = "40.90%tile";
        } else if ($score == 7) {
            $pp = "39.30%tile";
        } else if ($score == 6) {
            $pp = "37.70%tile";
        } else if ($score == 5) {
            $pp = "36.10%tile";
        } else if ($score == 4) {
            $pp = "34.50%tile";
        } else if ($score == 3) {
            $pp = "32.90%tile";
        } else if ($score == 2) {
            $pp = "31.30%tile";
        } else if ($score == 1) {
            $pp = "29.70%tile";
        } else if ($score == 0) {
            $pp = "28.10%tile";
        } else if ($score == -1) {
            $pp = "26.50%tile";
        } else if ($score == -2) {
            $pp = "24.90%tile";
        } else if ($score == -3) {
            $pp = "23.30%tile";
        } else if ($score >= -68 && $score <= -4) {
                $pp = "0%tile";
        }  
    }
    else if ($shift == 3) {
        if ($score >= 126 && $score <= 204) {
            $pp = "100%tile";
        } else if ($score == 125) {
            $pp = "99.99%tile";
        } else if ($score == 124) {
            $pp = "99.98%tile";
        } else if ($score == 123) {
            $pp = "99.97%tile";
        } else if ($score == 122 || $score == 121) {
            $pp = "99.96%tile";
        } else if ($score == 120) {
            $pp = "99.95%tile";
        } else if ($score == 119) {
            $pp = "99.94%tile";
        } else if ($score == 118) {
            $pp = "99.93%tile";
        } else if ($score == 117 || $score == 116) {
            $pp = "99.92%tile";
        } else if ($score == 115) {
            $pp = "99.91%tile";
        } else if ($score == 114) {
            $pp = "99.90%tile";
        } else if ($score == 113) {
            $pp = "99.88%tile";
        } else if ($score == 112) {
            $pp = "99.85%tile";
        } else if ($score == 111) {
            $pp = "99.83%tile";
        } else if ($score == 110) {
            $pp = "99.80%tile";
        } else if ($score == 109) {
            $pp = "99.78%tile";
        } else if ($score == 108) {
            $pp = "99.75%tile";
        } else if ($score == 107) {
            $pp = "99.73%tile";
        } else if ($score == 106) {
            $pp = "99.70%tile";
        } else if ($score == 105) {
            $pp = "99.68%tile";
        } else if ($score == 104) {
            $pp = "99.65%tile";
        } else if ($score == 103) {
            $pp = "99.62%tile";
        } else if ($score == 102) {
            $pp = "99.60%tile";
        } else if ($score == 101) {
            $pp = "99.57%tile";
        } else if ($score == 100) {
            $pp = "99.55%tile";
        } else if ($score == 99) {
            $pp = "99.52%tile";
        } else if ($score == 98) {
            $pp = "99.50%tile";
        } else if ($score == 97) {
            $pp = "99.47%tile";
        } else if ($score == 96) {
            $pp = "99.44%tile";
        } else if ($score == 95) {
            $pp = "99.41%tile";
        }else if ($score == 94) {
            $pp = "99.38%tile";
        }else if ($score == 93) {
            $pp = "99.35%tile";
        }else if ($score == 92) {
            $pp = "99.32%tile";
        }else if ($score == 91) {
            $pp = "99.29%tile";
        }else if ($score == 90) {
            $pp = "99.26%tile";
        }else if ($score == 89) {
            $pp = "99.23%tile";
        }else if ($score == 88) {
            $pp = "99.20%tile";
        } else if ($score == 87) {
            $pp = "99.17%tile";
        } else if ($score == 86) {
            $pp = "99.14%tile";
        } else if ($score == 85) {
            $pp = "99.11%tile";
        } else if ($score == 84) {
            $pp = "99.08%tile";
        } else if ($score == 83) {
            $pp = "99.05%tile";
        } else if ($score == 82) {
            $pp = "99.02%tile";
        } else if ($score == 81) {
            $pp = "98.99%tile";
        } else if ($score == 80) {
            $pp = "98.84%tile";
        } else if ($score == 79) {
            $pp = "98.69%tile";
        } else if ($score == 78) {
            $pp = "98.54%tile";
        } else if ($score == 77) {
            $pp = "98.39%tile";
        } else if ($score == 76) {
            $pp = "98.24%tile";
        } else if ($score == 75) {
            $pp = "98.09%tile";
        } else if ($score == 74) {
            $pp = "97.94%tile";
        } else if ($score == 73) {
            $pp = "97.64%tile";
        } else if ($score == 72) {
            $pp = "97.34%tile";
        } else if ($score == 71) {
            $pp = "97.04%tile";
        } else if ($score == 70) {
            $pp = "96.74%tile";
        } else if ($score == 69) {
            $pp = "96.44%tile";
        } else if ($score == 68) {
            $pp = "96.14%tile";
        } else if ($score == 67) {
            $pp = "95.84%tile";
        } else if ($score == 66) {
            $pp = "95.54%tile";
        } else if ($score == 65) {
            $pp = "95.24%tile";
        } else if ($score == 64) {
            $pp = "94.94%tile";
        } else if ($score == 63) {
            $pp = "94.69%tile";
        } else if ($score == 62) {
            $pp = "94.44%tile";
        } else if ($score == 61) {
            $pp = "94.19%tile";
        } else if ($score == 60) {
            $pp = "93.94%tile";
        } else if ($score == 59) {
            $pp = "93.69%tile";
        } else if ($score == 58) {
            $pp = "93.44%tile";
        } else if ($score == 57) {
            $pp = "93.19%tile";
        } else if ($score == 56) {
            $pp = "92.94%tile";
        } else if ($score == 55) {
            $pp = "92.69%tile";
        } else if ($score == 54) {
            $pp = "92.44%tile";
        } else if ($score == 53) {
            $pp = "92.19%tile";
        } else if ($score == 52) {
            $pp = "91.94%tile";
        } else if ($score == 51) {
            $pp = "91.69%tile";
        } else if ($score == 50) {
            $pp = "91.44%tile";
        } else if ($score == 49) {
            $pp = "91.19%tile";
        } else if ($score == 48) {
            $pp = "90.94%tile";
        } else if ($score == 47) {
            $pp = "90.69%tile";
        } else if ($score == 46) {
            $pp = "90.44%tile";
        } else if ($score == 45) {
            $pp = "89.64%tile";
        } else if ($score == 44) {
            $pp = "88.44%tile";
        } else if ($score == 43) {
            $pp = "88.04%tile";
        } else if ($score == 42) {
            $pp = "87.24%tile";
        } else if ($score == 41) {
            $pp = "86.44%tile";
        } else if ($score == 40) {
            $pp = "85.64%tile";
        } else if ($score == 39) {
            $pp = "85.00%tile";
        } else if ($score == 38) {
            $pp = "84.02%tile";
        } else if ($score == 37) {
            $pp = "83.04%tile";
        } else if ($score == 36) {
            $pp = "82.06%tile";
        } else if ($score == 35) {
            $pp = "81.08%tile";
        } else if ($score == 34) {
            $pp = "80.10%tile";
        } else if ($score == 33) {
            $pp = "79.40%tile";
        } else if ($score == 32) {
            $pp = "78.70%tile";
        } else if ($score == 31) {
            $pp = "78.00%tile";
        } else if ($score == 30) {
            $pp = "77.30%tile";
        } else if ($score == 29) {
            $pp = "76.60%tile";
        } else if ($score == 28) {
            $pp = "75.90%tile";
        } else if ($score == 27) {
            $pp = "75.20%tile";
        } else if ($score == 26) {
            $pp = "74.50%tile";
        } else if ($score == 25) {
            $pp = "72.90%tile";
        } else if ($score == 24) {
            $pp = "71.30%tile";
        } else if ($score == 23) {
            $pp = "69.70%tile";
        } else if ($score == 22) {
            $pp = "68.10%tile";
        } else if ($score == 21) {
            $pp = "66.50%tile";
        } else if ($score == 20) {
            $pp = "64.90%tile";
        } else if ($score == 19) {
            $pp = "63.30%tile";
        } else if ($score == 18) {
            $pp = "61.70%tile";
        } else if ($score == 17) {
            $pp = "60.10%tile";
        } else if ($score == 16) {
            $pp = "58.50%tile";
        } else if ($score == 15) {
            $pp = "56.90%tile";
        } else if ($score == 14) {
            $pp = "55.30%tile";
        } else if ($score == 13) {
            $pp = "53.70%tile";
        } else if ($score == 12) {
            $pp = "52.10%tile";
        } else if ($score == 11) {
            $pp = "50.50%tile";
        } else if ($score == 10) {
            $pp = "48.90%tile";
        } else if ($score == 9) {
            $pp = "47.30%tile";
        } else if ($score == 8) {
            $pp = "45.70%tile";
        } else if ($score == 7) {
            $pp = "44.10%tile";
        } else if ($score == 6) {
            $pp = "42.50%tile";
        } else if ($score == 5) {
            $pp = "40.90%tile";
        } else if ($score == 4) {
            $pp = "39.30%tile";
        } else if ($score == 3) {
            $pp = "37.70%tile";
        } else if ($score == 2) {
            $pp = "36.10%tile";
        } else if ($score == 1) {
            $pp = "34.50%tile";
        } else if ($score == 0) {
            $pp = "32.90%tile";
        } else if ($score == -1) {
            $pp = "31.30%tile";
        } else if ($score == -2) {
            $pp = "29.70%tile";
        } else if ($score == -3) {
            $pp = "28.10%tile";
        } else if ($score == -4) {
            $pp = "26.50%tile";
        } else if ($score == -5) {
            $pp = "24.90%tile";
        } else if ($score == -6) {
            $pp = "23.30%tile";
        } else if ($score >= -68 && $score <= -7) {
                $pp = "0%tile";
        }  
    }
    return $pp;
}

function sendJson($data, $http_code = 200) {
    header('Content-type: application/json');
    http_response_code($http_code);
    echo json_encode($data);
    die;
}

function _t($str, $remove_extra = '', $remove_extra_by = '') {
    $str = str_replace('&nbsp;', ' ', $str);
    $str = str_replace($remove_extra, $remove_extra_by, $str);
    $str = html_entity_decode($str, ENT_QUOTES | ENT_COMPAT , 'UTF-8');
    $str = html_entity_decode($str, ENT_HTML5, 'UTF-8');
    $str = html_entity_decode($str);
    $str = htmlspecialchars_decode($str);
    $str = strip_tags($str);
    return trim($str);
}
