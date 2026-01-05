<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserCatResult;
use App\Models\College;
use App\Models\User;
use Gate;

class DashboardController extends Controller
{
    public function index()
    {
        $student_count = UserCatResult::all()
            ->map(fn($r) => json_decode($r->data, true)['details']['Application No'] ?? null)
            ->filter()
            ->unique()
            ->count();
        $college_count = College::count();
        $user_count = User::where('id', '!=', 1)->count();
        return view('backend.dashboard.index', compact('student_count', 'college_count', 'user_count'));
    }
}