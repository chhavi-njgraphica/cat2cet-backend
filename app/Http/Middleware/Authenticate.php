<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // If user is logged in, redirect based on role
            if (auth()->check()) {
                $role = auth()->user()->role; // assuming your User model has a `role` attribute

                switch ($role) {
                    case 'admin':
                        return route('admin.dashboard');
                    // case 'vendor':
                    //     return route('vendor.dashboard');
                    // case 'user':
                    //     return route('user.dashboard'); // optional if needed
                }
            }

            // If not logged in, guess route based on path
            if ($request->is('vendor') || $request->is('vendor/*')) {
                return route('vendor.signin');
            } elseif ($request->is('admin') || $request->is('admin/*')) {
                return route('login');
            }

            return route('login'); // default fallback
        }
    }
}
