<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckOtpVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not logged in, allow access to login page
        if (!$user) {
            return redirect()->route('login');
        }
 if (session()->get('logged_in_via_passage')) {
        return $next($request);
    }
        // If user has not verified OTP and is trying to access another page, send them back to login
        if (!$user->otp_verified && !$request->route()->named('otp.verify') && !$request->route()->named('verifyotp')) {
            Auth::logout(); // Force logout since OTP is not verified
            return redirect()->route('login')->with('error', 'You need to verify OTP to continue.');
        }

        return $next($request);
    }
}