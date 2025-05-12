<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Check if the user is authenticated and has a supplier_id
        if (Auth::check() && (Auth::user()->roledata->role_slug == config('constants.roles.CLIENT_SERVICE_EXECUTIVE') || Auth::user()->roledata->role_slug == config('constants.roles.CLIENTMASTERCLIENT')) || ($user->is_owner || $user->is_dev)) {
            return $next($request); 
        }
        
        return redirect()->route('login');
    }
}
