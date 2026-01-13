<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        // Redirect guest users to login
        if (!$user) {
            return redirect('/login');
        }

        $details = $user->details;

        if ($details) {
            // Determine roles
            $isSuperAdmin = (
                $details->department_id == 9 &&
                $details->division_id == 3 &&
                $details->role_id == 1 &&
                $details->permission_id == 1
            );

            $isSezadUser = ($details->department_id == 12 &&
                        $details->role_id == 2);
            $isLocator = ($details->role_id == 3);
            $isSezadUser = null;
            $isLocator = ($details->role_id == 2);
            $isOsac = ($details->position_id == 36 &&
                        $details->department_id == 12 &&
                        $details->role_id == 2 &&
                        $details->permission_id == 2);
            $isCco= ($details->position_id == 37 &&
                     $details->department_id == 12 &&
                     $details->role_id == 2 &&
                     $details->permission_id == 2); 
           
            // Super Admin: allow only /dashboard and /sezad
            if ($isSuperAdmin) {
                if (! $request->is('dashboard') && ! $request->is('sezad') && ! $request->is('sezad/*')) {
                    return redirect('/dashboard');
                }
            }
            // SEZAD user: allow only /sezad
            elseif ($isSezadUser) {
                if (! $request->is('sezad') && ! $request->is('sezad/*')) {
                    return redirect('/sezad');
                }
            }
            // // Locator user: allow only /locator
            elseif ($isLocator) {
                
                     return redirect('/bdd');
             
             } elseif($isCco)
             {
                  return redirect('/cco');
             }elseif($isOsac){
                return redirect('/osac');
             }
            // Optionally: handle users with unknown roles
            else {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
