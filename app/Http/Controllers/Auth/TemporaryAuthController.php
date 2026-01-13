<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemporaryAuthController extends Controller
{
    /**
     * Handle login for temporary users.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Use normal 'password' key; getAuthPassword() maps it to temp_password
        $credentials = $request->only('email', 'password');

        if (Auth::guard('temp')->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect temporary user to dashboard
            return redirect()->intended('/temp/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid temporary account credentials.',
        ]);
    }

    /**
     * Logout temporary user
     */
    public function logout(Request $request)
    {
        Auth::guard('temp')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/temp/login');
    }
}
