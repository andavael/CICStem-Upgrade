<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApprovedTutor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tutor = Auth::guard('tutor')->user();

        // Check if tutor is approved
        if (!$tutor || !$tutor->is_approved) {
            return redirect()->route('tutor.pending')
                ->with('warning', 'Your account is still pending approval. Please wait for admin approval.');
        }

        // Check if tutor account is active
        if ($tutor->status === 'Inactive') {
            Auth::guard('tutor')->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        return $next($request);
    }
}