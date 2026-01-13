<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TempAuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('temp-login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('temp')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Logged in successfully');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('temp')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('temp.login');
    }
}
