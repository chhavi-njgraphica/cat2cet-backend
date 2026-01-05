<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\UserCatResult;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserCatResultController extends Controller
{
    // public function index()
    // {
    //     Log::info('🔹 UserCatResultController@index called');

    //     $record = UserCatResult::query()->latest()->first();

    //     if (!$record) {
    //         Log::warning('⚠️ No result found in database');
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No result found'
    //         ], 404);
    //     }

    //     Log::info('✅ Found user result record', [
    //         'id' => $record->id,
    //         'url' => $record->url
    //     ]);

    //     $student_data = json_decode($record->data);
    //     $percentileString = $student_data->details->percentile ?? null;

    //     Log::info('🎯 Extracted percentile string', [
    //         'percentileString' => $percentileString
    //     ]);

    //     if (!$percentileString) {
    //         Log::error('❌ Percentile string missing in student data');
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No percentile found in result'
    //         ], 400);
    //     }

    //     // Extract numeric values like 65 and 70 from "65%tile - 70%tile"
    //     preg_match_all('/\d+/', $percentileString, $matches);
    //     $minPercentile = isset($matches[0][0]) ? (float) $matches[0][0] : null;
    //     $maxPercentile = isset($matches[0][1]) ? (float) $matches[0][1] : $minPercentile;

    //     $percentile = $minPercentile ?? 0;

    //     Log::info('📊 Parsed percentile range', [
    //         'minPercentile' => $minPercentile,
    //         'maxPercentile' => $maxPercentile,
    //         'selectedPercentile' => $percentile,
    //     ]);

    //     $colleges = College::all();
    //     Log::info('🏫 Total colleges fetched', ['count' => $colleges->count()]);

    //     $suggested = $colleges->filter(function ($college) use ($percentile) {
    //         if (!$college->percentile_between) return false;

    //         $range = explode('-', $college->percentile_between);
    //         if (count($range) !== 2) return false;

    //         $min = (float) trim($range[0]);
    //         $max = (float) trim($range[1]);

    //         return $percentile >= $min && $percentile <= $max;
    //     });

    //     if ($suggested->isEmpty()) {
    //         Log::warning('⚠️ No colleges found in percentile range', [
    //             'percentile' => $percentile
    //         ]);

    //         $closest = $colleges->map(function ($college) use ($percentile) {
    //             if (!$college->percentile_between) return null;

    //             $range = explode('-', $college->percentile_between);
    //             if (count($range) !== 2) return null;

    //             $min = (float) trim($range[0]);
    //             $max = (float) trim($range[1]);
    //             $center = ($min + $max) / 2;
    //             $distance = abs($center - $percentile);

    //             return [
    //                 'college' => $college,
    //                 'distance' => $distance,
    //             ];
    //         })
    //         ->filter()
    //         ->sortBy('distance')
    //         ->take(5)
    //         ->pluck('college');

    //         $suggested = $closest;

    //         Log::info('✨ Showing closest colleges', [
    //             'closest_colleges' => $suggested->pluck('college_name')
    //         ]);
    //     } else {
    //         Log::info('✅ Found matching colleges', [
    //             'suggested_colleges' => $suggested->pluck('college_name')
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'url' => $record->url,
    //         'result' => json_decode($record->data),
    //         'percentile' => $percentile,
    //         'suggested_colleges' => $suggested->values(),
    //     ]);
    // }

    public function catResult(Request $request)
    {
        $url = $request->url;

        if (
            empty($url) ||
            !strstr(parse_url($url, PHP_URL_HOST), 'cdn.digialm.com') ||
            !strstr($url, 'touchstone/AssessmentQPHTMLMode1')
        ) {
            return response()->json(['success' => false, 'error' => 'Enter a valid URL. ex: https://cdn.digialm.com/.../.html']);
        }

        $existing = UserCatResult::where('url', $url)->first();

        $result = getStudentResult($url);

        if (count(@$result['details']) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Enter a valid URL. ex: https://cdn.digialm.com/.../.html'
            ]);
        }

        $data = json_encode($result);

        if ($existing) {
            $existing->data = $data;
            $existing->user_id = $request->user_id;
            $existing->url = $url;
            $existing->save();
        } else {
            $cat_result = new UserCatResult;
            $cat_result->url = $url;
            $cat_result->user_id = $request->user_id;
            $cat_result->data = $data;
            $cat_result->save();
        }

        // if ($existing) {
        //     $result = json_decode($existing->data, true);
        // } else {
        //     // Otherwise fetch fresh result
        //     $result = getStudentResult($url);

        //     if (count(@$result['details']) > 0) {
        //         $data = json_encode($result);

        //         $cat_result = new UserCatResult;
        //         $cat_result->url = $url;
        //         $cat_result->user_id = $request->user_id;
        //         $cat_result->data = $data;
        //         $cat_result->save();
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'error' => 'Enter a valid URL. ex: https://cdn.digialm.com/.../.html'
        //         ]);
        //     }
        // }

        // Continue processing percentile and suggested colleges
        $percentileString = $result['percentile'] ?? null;

        preg_match_all('/\d+/', $percentileString, $matches);
        $minPercentile = isset($matches[0][0]) ? (float) $matches[0][0] : null;
        $maxPercentile = isset($matches[0][1]) ? (float) $matches[0][1] : $minPercentile;
        $percentile = $minPercentile ?? 0;

        $colleges = College::all();

        $suggested = $colleges->filter(function ($college) use ($percentile) {
            if (!$college->percentile_between) return false;

            $range = explode('-', $college->percentile_between);
            if (count($range) !== 2) return false;

            $min = (float) trim($range[0]);
            $max = (float) trim($range[1]);

            return $percentile >= $min && $percentile <= $max;
        });

        if ($suggested->isEmpty()) {
            $closest = $colleges->map(function ($college) use ($percentile) {
                if (!$college->percentile_between) return null;

                $range = explode('-', $college->percentile_between);
                if (count($range) !== 2) return null;

                $min = (float) trim($range[0]);
                $max = (float) trim($range[1]);
                $center = ($min + $max) / 2;
                $distance = abs($center - $percentile);

                return [
                    'college' => $college,
                    'distance' => $distance,
                ];
            })
            ->filter()
            ->sortBy('distance')
            ->pluck('college');

            $suggested = $closest;
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'result' => $result,
            'percentile' => $percentile,
            'suggested_colleges' => $suggested->values(),
        ]);
    }



    public function downloadCatResultPdf(Request $request)
    {
        $url = $request->url;

        if (
            empty($url) ||
            !strstr(parse_url($url, PHP_URL_HOST), 'cdn.digialm.com') ||
            !strstr($url, 'touchstone/AssessmentQPHTMLMode1')
        ) {
            return response()->json(['success' => false, 'error' => 'Enter a valid URL.']);
        }

        $result = getStudentResult($url);

        if (empty($result) || count(@$result['details']) === 0) {
            return response()->json(['success' => false, 'error' => 'Invalid result data.']);
        }

        $percentileString = $result['percentile'] ?? null;
        preg_match_all('/\d+/', $percentileString, $matches);
        $percentile = isset($matches[0][0]) ? (float) $matches[0][0] : 0;

        $colleges = College::all();

        $suggested = $colleges->filter(function ($college) use ($percentile) {
            if (!$college->percentile_between) return false;
            $range = explode('-', $college->percentile_between);
            if (count($range) !== 2) return false;
            $min = (float) trim($range[0]);
            $max = (float) trim($range[1]);
            return $percentile >= $min && $percentile <= $max;
        });

        if ($suggested->isEmpty()) {
            $closest = $colleges->map(function ($college) use ($percentile) {
                if (!$college->percentile_between) return null;
                $range = explode('-', $college->percentile_between);
                if (count($range) !== 2) return null;
                $min = (float) trim($range[0]);
                $max = (float) trim($range[1]);
                $center = ($min + $max) / 2;
                $distance = abs($center - $percentile);
                return ['college' => $college, 'distance' => $distance];
            })
            ->filter()
            ->sortBy('distance')
            ->take(5)
            ->pluck('college');
            $suggested = $closest;
        }

        $pdf = Pdf::loadView('pdf.cat_result', [
            'result' => $result,
            'percentile' => $percentileString,
            'suggested' => $suggested,
            'url' => $url,
        ]);

        // 🔹 Return the PDF for download
        return $pdf->download('cat_result.pdf');
    }


}
