<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Enforce strict role-based route access.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin always has full access
        if ($user->hasRole('SUPER_ADMIN')) {
            return $next($request);
        }

        // If specific roles are passed, verify user has at least one of them
        if (!empty($roles) && $user->hasRole($roles)) {
            return $next($request);
        }

        // If user is a student trying to access admin routes, redirect to learner portal
        if ($user->isStudent()) {
            return redirect()->route('my-learning')->with('warning', 'Access restricted to learner workspace.');
        }

        abort(403, sprintf('Unauthorized access. Your role (%s) does not permit access to this module.', $user->roleLabel()));
    }
}
