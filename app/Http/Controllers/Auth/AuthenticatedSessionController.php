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

        $request->session()->put('user_details', $user_details);

        // Handle 2FA
        if (Features::enabled(Features::twoFactorAuthentication()) &&
            $user->hasEnabledTwoFactorAuthentication()) {

            $request->session()->put([
                'login.id' => $user->id,
                'login.remember' => $request->boolean('remember'),
            ]);

            return to_route('two-factor.login');
        }

        // Login user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Redirect rules
        if ($user_details &&
            $user_details->department_id == 9 &&
            $user_details->division_id == 3 &&
            $user_details->role_id == 1 &&
            $user_details->permission_id == 1) {

            return redirect()->intended(route('dashboard', false));
        }

        if ($user_details && $user_details->role_id == 3) {
            return redirect()->intended('/locator');
        }

        if ($user_details &&
            $user_details->position_id == 36 &&
            $user_details->department_id == 12) {

            return redirect()->intended('sezad/osac');
        }

        if ($user_details &&
            $user_details->department_id == 12 &&
            $user_details->position_id == 37 &&
            $user_details->role_id == 2 &&
            $user_details->permission_id == 2) {

            return redirect()->intended('sezad/cco');
        }

        if ($user_details &&
            $user_details->department_id == 10 &&
            $user_details->role_id == 2 &&
            $user_details->position_id == 53 &&   // FIXED HERE
            $user_details->permission_id == 2) {

            return redirect()->intended('fsd/finance');
        }

        if ($user_details &&
            $user_details->department_id == 12 &&
            $user_details->user_function_id == 5 &&
            $user_details->role_id == 2 &&
            $user_details->permission_id == 1) {

            return redirect()->intended('sezad/manager');
        }

        if ($user_details &&
            $user_details->department_id == 12 &&
            $user_details->division_id === null &&
            $user_details->role_id == 2 &&
            $user_details->permission_id == 2) {

            return redirect()->intended('/sezad');
        }else{
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($user_details &&
            $user_details->department_id == 5 &&
            $user_details->position_id == 54 &&
            $user_details->role_id == 2 &&
            $user_details->permission_id == 2) {

            return redirect()->intended(route('bdd.Dashboard', false));
        }

        // Default fallback redirect (avoid returning nothing)
        return redirect()->intended('/');
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
