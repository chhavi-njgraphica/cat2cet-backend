<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\UserXatResult;
use App\Models\XatCollege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserXatResultController extends Controller
{    
    public function xatResult(Request $request)
    {
        $url = $request->url;

        if (
            empty($url) ||
            !strstr(parse_url($url, PHP_URL_HOST), 'g21.digialm.com') ||
            !strstr($url, 'touchstone/AssessmentQPHTMLMode1')
        ) {
            return response()->json(['success' => false, 'error' => 'Enter a valid URL. ex: https://cdn.digialm.com/.../.html']);
        }

        $existing = UserXatResult::where('user_id', $request->user_id)->first();

        $result = getXatStudentResult($url);

        if (count(@$result['details']) === 0) {
            return response()->json([
                'success' => false,
                'error' => 'Enter a valid URL. ex: https://cdn.digialm.com/.../.html'
            ]);
        }

        $data = json_encode($result);

        if ($existing) {
            // same user → replace url
            $existing->url = $url;
            $existing->data = $data;
            $existing->save();
        } else {
            // new user → create new entry
            UserXatResult::create([
                'user_id' => $request->user_id,
                'url' => $url,
                'data' => $data,
            ]);
        }    

        // Continue processing percentile and suggested colleges
        $percentileString = $result['percentile'] ?? null;

        preg_match_all('/\d+/', $percentileString, $matches);
        $minPercentile = isset($matches[0][0]) ? (float) $matches[0][0] : null;
        $maxPercentile = isset($matches[0][1]) ? (float) $matches[0][1] : $minPercentile;
        $percentile = $minPercentile ?? 0;

        $colleges = XatCollege::all();

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



    public function downloadXatResultPdf(Request $request)
    {
        $url = $request->url;

        if (
            empty($url) ||
            !strstr(parse_url($url, PHP_URL_HOST), 'g21.digialm.com') ||
            !strstr($url, 'touchstone/AssessmentQPHTMLMode1')
        ) {
            return response()->json(['success' => false, 'error' => 'Enter a valid URL.']);
        }

        $result = getXatStudentResult($url);

        if (empty($result) || count(@$result['details']) === 0) {
            return response()->json(['success' => false, 'error' => 'Invalid result data.']);
        }

        $percentileString = $result['percentile'] ?? null;
        preg_match_all('/\d+/', $percentileString, $matches);
        $percentile = isset($matches[0][0]) ? (float) $matches[0][0] : 0;

        $colleges = XatCollege::all();

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

        Log::info('XAT PDF result data', [
        'result' => $result,
        'percentile' => $percentile,
    ]);


        $pdf = Pdf::loadView('pdf.xat_result', [
            'result' => $result,
            'percentile' => $percentileString,
            'suggested' => $suggested,
            'url' => $url,
        ]);

        // 🔹 Return the PDF for download
        return $pdf->download('xat_result.pdf');
    }


}
