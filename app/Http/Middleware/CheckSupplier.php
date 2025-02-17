<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSupplier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // Check if the user is authenticated and has a supplier_id
        if (Auth::check() && Auth::user()->roledata->role_slug == config('constants.roles.MASTERCLIENT') || ($user->is_owner || $user->is_dev)) {
            return $next($request); 
        }

        return redirect()->route('login');
    }
}

