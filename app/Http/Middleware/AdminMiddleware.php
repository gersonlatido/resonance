<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is logged in and has the 'Admin' position
        if (!auth()->user() || auth()->user()->position !== 'Admin') {
            return redirect('/'); // Redirect non-admin users to the home page
        }

        return $next($request); // Proceed if the user is an admin
    }
}

