<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:student,tutor,admin',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $credentials = $request->only('email', 'password');
        $userType = $request->input('user_type');
        $remember = $request->has('remember');

        // Attempt login based on user type
        if (Auth::guard($userType)->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Get authenticated user
            $user = Auth::guard($userType)->user();

            // Redirect based on user type and status
            switch ($userType) {
                case 'student':
                    if ($user->status === 'Active') {
                        return redirect()->intended(route('student.dashboard'));
                    } else {
                        Auth::guard('student')->logout();
                        return back()->with('error', 'Your account is inactive. Please contact administrator.');
                    }

                case 'tutor':
                    if ($user->status === 'Inactive') {
                        Auth::guard('tutor')->logout();
                        return back()->with('error', 'Your account is inactive. Please contact administrator.');
                    }
                    
                    if (!$user->is_approved) {
                        return redirect()->route('tutor.pending');
                    }
                    
                    return redirect()->intended(route('tutor.dashboard'));

                case 'admin':
                    // Admin always goes to dashboard (stats page)
                    return redirect()->intended(route('admin.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Determine which guard to logout
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        } elseif (Auth::guard('tutor')->check()) {
            Auth::guard('tutor')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}