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

            // Parse PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();
            $text = preg_replace("/[\s\x{00A0}]+/u", " ", $text);

            Log::info('PDF Text Snippet: ' . substr($text, 0, 500));

            preg_match('/Name of the Candidate\s*[:\-]?\s*([A-Z\s]+?)(?:\s+Admission Category|$)/i', $text, $nameMatch);
            preg_match('/Admission Category\s*[:\-]?\s*([A-Z\s]+?)(?:\s+Overall Percentile|$)/i', $text, $categoryMatch);
            preg_match('/Overall Percentile\s*[:\-]?\s*([0-9.]+)/i', $text, $percentileMatch);

            $name = isset($nameMatch[1]) ? trim(preg_replace('/\s+/', ' ', $nameMatch[1])) : null;
            $category = isset($categoryMatch[1]) ? trim(preg_replace('/\s+/', ' ', $categoryMatch[1])) : null;
            $percentile = isset($percentileMatch[1]) ? (float)$percentileMatch[1] : null;

            return response()->json([
                'name' => $name,
                'category' => $category,
                'overall_percentile' => $percentile,
            ]);

        } catch (\Exception $e) {
            Log::error('PDF parsing failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to parse PDF file',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    

    public function userSubmit(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'category' => 'required|string|max:100',
            'overall_percentile' => 'required|numeric|min:0|max:100',
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
                'overall_percentile' => $validated['overall_percentile'],
            ]
        );

        return response()->json([
            'message' => 'Snap user data submitted successfully',
            'data' => $snapUser,
        ], 201);
    }
}
