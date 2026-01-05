<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserXatResult;
use App\Models\User;
use App\Exports\UserXatResultExport;
use App\Exports\StudentResultExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Gate;

class XatStudentResultController extends Controller
{
    public function index(Request $request)
    {
        $query = UserXatResult::query();

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

        foreach ($results as $result) {
            $result->decoded_data = json_decode($result->data);
        }

        if ($request->has('export')) {
            return Excel::download(new StudentResultExport($results), 'student_results.xlsx');
        }

        return view('backend.xat-result.index', compact('results'));
    }

    public function show($id)
    {
        $record = UserXatResult::findOrFail($id);

        $result = json_decode($record->data);

        return view('backend.xat-result.show', compact('record', 'result'));
    }

    public function exportStudent($id)
    {
        $result = UserXatResult::with('user')->findOrFail($id);
        $result->decoded_data = json_decode($result->data);

        return Excel::download(new UserXatResultExport(collect([$result])), 'student-result-' . $id . '.xlsx');
    }
}
