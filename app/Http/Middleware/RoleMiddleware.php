<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $userRole = strtolower((string) (auth()->user()->position ?? ''));
        $allowedRoles = array_map(fn ($role) => strtolower(trim($role)), $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}