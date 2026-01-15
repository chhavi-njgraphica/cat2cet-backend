<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSnapResult;
use App\Models\User;
use App\Exports\UserSnapResultExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Gate;

class SnapStudentResultController extends Controller
{
    public function index(Request $request)
    {
        $query = UserSnapResult::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->where('id', '!=', 1)
                ->whereBetween('created_at', [$startDate, $endDate]);

        }
        elseif ($request->filled('start_date') && !$request->filled('end_date')) {

            $date = Carbon::parse($request->start_date)->startOfDay();

            $query->where('id', '!=', 1)
                ->whereDate('created_at', $date);
        }
        $results = $query->get();

        if ($request->has('export')) {
            return Excel::download(new UserSnapResultExport($results), 'snap_student_results.xlsx');
        }

        return view('backend.snap-result.index', compact('results'));
    }

    public function show($id)
    {
        $record = UserSnapResult::findOrFail($id);

        $result = json_decode($record->data);

        return view('backend.snap-result.show', compact('record', 'result'));
    }
    
    public function pdfView($id)
    {
        $record = UserSnapResult::findOrFail($id);

        // Make full path inside storage/app
        $fullPath = storage_path('app/private/' . $record->pdf_path);

        // Check file exists
        abort_unless(file_exists($fullPath), 404);

        // Return inline PDF
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }
}
