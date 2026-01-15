<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SnapUser;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class SnapController extends Controller
{
    public function pdfUpload(Request $request)
    {
        // Validate uploaded PDF
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:5120', // max 5MB
        ]);

        try {
            $file = $request->file('pdf');
            $filePath = $file->getRealPath(); // Temp file path

            $pdfPath = $file->store('snap_pdfs'); 
            // Parse PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = preg_replace("/[\s\x{00A0}]+/u", " ", $text);

            // Log::info('PDF Text Snippet: ' . substr($text, 0, 500));

            preg_match('/Name of the Candidate\s*[:\-]?\s*([A-Z\s]+?)(?:\s+Admission Category|$)/i', $text, $nameMatch);
            preg_match('/Admission Category\s*[:\-]?\s*([A-Z\s]+?)(?:\s+Overall Percentile|$)/i', $text, $categoryMatch);
            preg_match('/Overall Percentile\s*[:\-]?\s*([0-9.]+)/i', $text, $percentileMatch);

            $name = isset($nameMatch[1]) ? trim(preg_replace('/\s+/', ' ', $nameMatch[1])) : null;
            $category = isset($categoryMatch[1]) ? trim(preg_replace('/\s+/', ' ', $categoryMatch[1])) : null;
            $percentile = isset($percentileMatch[1]) ? (float)$percentileMatch[1] : null;

            $num = '-?\d+(?:\.\d+)?';
            $patternEnglish = "/General English.*?(?:$num)\\s+($num)(?:\\s+($num))?(?:\\s+($num))?/i";
            $patternLogical = "/Analytical\\s*&\\s*Logical\\s*Reasoning.*?(?:$num)\\s+($num)(?:\\s+($num))?(?:\\s+($num))?/i";
            $patternQuant   = "/Quantitative.*?(?:$num)\\s+($num)(?:\\s+($num))?(?:\\s+($num))?/i";

            preg_match($patternEnglish, $text, $english);
            preg_match($patternLogical, $text, $logical);
            preg_match($patternQuant,   $text, $quant);

            $totals = [];
            $totalEnglish = [];
            $totalLogical = [];
            $totalQuant = [];

            for ($i = 1; $i <= 3; $i++) {
                if (isset($english[$i], $logical[$i], $quant[$i])) {
                    $totals[] = (float)$english[$i] + (float)$logical[$i] + (float)$quant[$i];
                    $totalEnglish[] = (float)$english[$i];
                    $totalLogical[] = (float)$logical[$i];
                    $totalQuant[] = (float)$quant[$i];
                }
            }

            $englishTotalScore = $totalEnglish ? max($totalEnglish) : null;
            $logicalTotalScore = $totalLogical ? max($totalLogical) : null;
            $quantTotalScore = $totalQuant ? max($totalQuant) : null;
            // Log::info('English: ' . $englishTotal);


            $maxTotalScore = $totals ? max($totals) : null;
            return response()->json([
                'name' => $name,
                'category' => $category,
                'english' => $englishTotalScore,
                'logical' => $logicalTotalScore,
                'quant' => $quantTotalScore,
                'overall_percentile' => $percentile,
                'max_score' => $maxTotalScore,
                'pdf_path' => $pdfPath,
            ]);

        } catch (\Exception $e) {
            Log::error('PDF parsing failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to parse PDF file',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function manualEntry(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'english' => 'required|numeric|min:0',
            'logical' => 'required|numeric|min:0',
            'quant' => 'required|numeric|min:0',
            'overall_percentile' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0',
        ]);

        return response()->json([
            'category' => $validated['category'],
            'english' => (float) $validated['english'],
            'logical' => (float) $validated['logical'],
            'quant' => (float) $validated['quant'],
            'overall_percentile' => (float) $validated['overall_percentile'],
            'max_score' => (float) $validated['max_score'],
            'type' => 'manual',
        ]);
    }

    

    public function userSubmit(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'category' => 'required|string|max:100',
            'english' => 'required',
            'logical' => 'required',
            'quant' => 'required',
            'overall_percentile' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100',
            'pdf_path' => 'nullable|string',
        ]);

        $snapUser = SnapUser::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'whatsapp_number' => $validated['whatsapp_number'],
            ]
        );

        $snapUser->snap_result()->updateOrCreate(
            ['snap_user_id' => $snapUser->id],
            [
                'category' => $validated['category'],
                'english' => $validated['english'],
                'logical' => $validated['logical'],
                'quant' => $validated['quant'],
                'overall_percentile' => $validated['overall_percentile'],
                'max_score' => $validated['max_score'],
                'pdf_path' => $validated['pdf_path'],
            ]
        );

        return response()->json([
            'message' => 'Snap user data submitted successfully',
            'data' => $snapUser,
        ], 201);
    }
}
