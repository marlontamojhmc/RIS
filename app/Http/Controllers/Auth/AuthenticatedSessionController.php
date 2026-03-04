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
use App\Models\UserDetails\UserDetail;

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
    // Validate credentials
    $user = $request->validateCredentials();

    // Fetch user details
    $userDetails = UserDetail::where('user_id', $user->id)->first();

    // 2FA check
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

    // Log the user in
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    // Flash welcome message
    $request->session()->flash('success', 'Welcome back! Login successful.');

    // Store user details in session
    $request->session()->put('user_details', $userDetails);

    // Clear any previously stored intended URL
    $request->session()->forget('url.intended');
    // dd($userDetails->isRO());
    // Role-based redirects
    if (
        $userDetails->isOsac() ||
        $userDetails->isSezadManager() ||
        $userDetails->isCCO() ||
        $userDetails->isRO()
    ) {
        return redirect('/sezad');
    }

    // Default redirect
    return redirect()->route('locator');
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
