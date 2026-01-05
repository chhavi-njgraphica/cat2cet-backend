<?php

namespace App\Http\Controllers;

use App\Mail\Mail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;
use Illuminate\Support\Facades\Auth;
use App\Models\PasswordReset;

class AuthController extends Controller
{

    public function signin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = $request->only('email', 'password');
        $remember_me = $request->has('remember');
        $login= Auth::attempt($user,$remember_me);

        if ($login) {
            $user = Auth::user();
            return redirect()->route('backend.dashboard');
        } else {
            $msg = 'Login details are not valid';
            $request->session()->flash('error-msg', $msg);
            return redirect()->route('login');
        }
    }

    public function signOut() {
        Session::flush();
        Auth::logout();
        return Redirect()->route('login');
    }

    public function login(){
        return view('backend.auth.login');
    }

    public function passwordForgot(){
        return view('auth.passwords.forgot');
    }

    public function passwordReset(){
        return view('auth.passwords.reset');
    }

    public function passwordResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $chquser = User::query()->where('email', '=', $request->post('email'))->first();
        if($chquser == null){
            $msg ='Email not registered';
            $request->session()->flash('error-msg',$msg);
            return redirect('forgot-password');
        }
       else{
            $chqotp = PasswordReset::query()->where('email', '=', $request->post('email'))->first();
            $random = rand(0000,9999);
            if($chqotp == null){
                $reset = new PasswordReset();
                $reset->email = $request->email;
                $reset->token =  $random;
                $reset->save();
            }else{
                $chqotp->token = $random;
                $chqotp->update();
            }

            $details = [
                'email' => $request->post('email'),
                'otp' => $random,
                'type'=>'otp'
            ];
            $subject=" MyCATKing: Validate OTP for Resetting the Password";

            \Mail::to($request->email)->send(new Mail($details,$subject));

            $msg ='Mail Sent to Your Email';
            $request->session()->flash('success-msg',$msg);
            Session::put('email',$request->email);
            return redirect('reset-password');
       }
    }

    public function newPassword(Request $request)
    {

        $request->validate([
            'otp' => 'required',
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);
        if($request->old_password == $request->new_password){
            $check = passwordReset::query()->where(['email'=>$request->email,'token'=>$request->otp])->first();

            if($check){
                $user = User::query()->where('email',$request->email)->first();
                $user->password = Hash::make($request->new_password);
                $update = $user->update();
                if($update){
                    $msg ='Password Update Successfully';
                    $request->session()->flash('success-msg',$msg);
                    return redirect()->route('login');
                }
            }else{
                $msg ='Otp is Incorrect.';
                $request->session()->flash('error-msg',$msg);
                return redirect('reset-password');
            }
        }else{
            $msg ='password does not match with confirm password';
            $request->session()->flash('error-msg',$msg);
            return redirect('reset-password');
        }
    }
}
