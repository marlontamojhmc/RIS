<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(LoginRequest $request): RedirectResponse
{
    $user = $request->validateCredentials();

    $user_details = DB::table('user_details')
        ->where('user_id', $user->id)
        ->first();

    // 2FA check (no session write yet)
    if (
        Features::enabled(Features::twoFactorAuthentication()) &&
        $user->hasEnabledTwoFactorAuthentication()
    ) {
        $request->session()->put([
            'login.id' => $user->id,
            'login.remember' => $request->boolean('remember'),
        ]);

        return to_route('two-factor.login');
    }

    // LOGIN FIRST
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    $request->session()->flash('success', 'Welcome back! Login successful.');
    //  THEN STORE SESSION DATA
    $request->session()->put('user_details', $user_details);
    // Redirect rules (unchanged)
     if (
        $user_details &&
        $user_details->department_id == 9 &&
        $user_details->division_id == 3 &&
        $user_details->role_id == 1 &&
        $user_details->permission_id == 1
    ) {
        return redirect()->intended(route('dashboard', false));
    }

    // ... rest unchanged

    return redirect()->intended(route('dashboard', false));
}

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
