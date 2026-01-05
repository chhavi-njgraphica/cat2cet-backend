<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\XatCollege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class XatCollegeController extends Controller
{
    public function index(){
        $college = XatCollege::get();

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
