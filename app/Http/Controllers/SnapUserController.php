<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SnapUser;
use App\Exports\SnapUserExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Gate;

class SnapUserController extends Controller
{
    public function index(Request $request)
    {
        $query = SnapUser::query();

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
            return Excel::download(new SnapUserExport($results), 'snap_users.xlsx');
        }

        return view('backend.snap-user.index', compact('results'));
    }

}