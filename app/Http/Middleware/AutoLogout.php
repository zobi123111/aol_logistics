<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
//     public function handle($request, Closure $next)
//     {
//         $user = Auth::user(); // Get the authenticated user
    
//         if ($user) {
//             // $sessionLifetime = env(2, 30); 
//              $sessionLifetime = env('SESSION_LIFETIME', 30) ;
//             $lastActivity = session('last_activity');
//             $currentTime = time();
//             // If last activity was more than the session lifetime, log the user out
//             // if ($lastActivity && now()->diffInMinutes(Carbon::createFromTimestamp($lastActivity)) >= $sessionLifetime) {
//                 if ($lastActivity && ($currentTime - $lastActivity > $sessionLifetime)) {

//                 // Update session_id to null in the database
//                 $user->session_id = null;
//                 $user->save();
    
//                 // Logout user and invalidate session
//                 Auth::logout();
//                 Session::flush();
//                 session()->invalidate();
//                 session()->regenerateToken();
//                 return redirect()->route('login'); 
//             }
    
//             // Update last activity timestamp
//             session(['last_activity' => time()]); 
//         }
    
//         return $next($request); 
//     }


public function handle($request, Closure $next)
{
    // if (Auth::check()) {
    //     $user = Auth::user();
    //     $sessionToken = $request->header('Authorization');
    //     $currentSession = session()->getId();
    //     dd($currentSession);
    //     if ($currentSession !== $user->session_id) {
    //         Auth::logout();
    //         return redirect()->route('login'); 
    //     }
    // }

    return $next($request);
}
}
