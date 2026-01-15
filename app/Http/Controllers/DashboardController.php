<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\UserCatResult;
use App\Models\UserXatResult;
use App\Models\College;
use App\Models\XatCollege;
use App\Models\User;
use App\Models\XatUser;
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
        $xat_student_count = UserXatResult::all()
            ->map(fn($r) => json_decode($r->data, true)['details']['XAT ID'] ?? null)
            ->filter()
            ->unique()
            ->count();    
        $college_count = College::count();
        $xat_college_count = XatCollege::count();
        $user_count = User::where('id', '!=', 1)->count();
        $xat_user_count = XatUser::count();
        return view('backend.dashboard.index', compact('student_count', 'xat_student_count', 'college_count', 'xat_college_count', 'user_count', 'xat_user_count'));
    }
}