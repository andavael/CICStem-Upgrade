<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on guard
                switch ($guard) {
                    case 'student':
                        return redirect()->route('student.dashboard');
                    case 'tutor':
                        $tutor = Auth::guard('tutor')->user();
                        if ($tutor->is_approved && $tutor->status === 'Active') {
                            return redirect()->route('tutor.dashboard');
                        }
                        return redirect()->route('tutor.pending');
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    default:
                        return redirect('/');
                }
            }
        }

        return $next($request);
    }
}