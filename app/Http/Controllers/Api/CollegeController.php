<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CollegeController extends Controller
{
    public function index(){
        $college = College::get();

        if (!$college) {
            return response()->json([
                'success' => false,
                'message' => 'No result found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'college' => $college,
        ]);
    }

}
