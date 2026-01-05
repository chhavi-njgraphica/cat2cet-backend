<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\XatUser;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserXatController extends Controller
{
    public function submit(Request $request){
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'whatsapp_number' => 'required|string|max:20',

        ]);

        $user = XatUser::where('email', $request->email)
            ->orWhere('whatsapp_number', $request->whatsapp_number)
            ->first();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'User already exists',
                'user' => $user,
                'new' => false,
            ]);
        }
        $user = XatUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User added successfully',
            'user' => $user,
            'new' => true,
        ]);
    }

}