<?php

namespace App\Http\Controllers\SEZAD\OneStopActionCenter\Clearances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class bring_in extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User) { 
            if ($user && method_exists($user, 'load')) {
                $user->load([
                    'details' => function ($query) {
                        $query->select(
                            'id',
                            'user_id',
                            'permission_id',
                            'role_id',
                            'department_id',
                            'division_id',
                            'user_function_id'
                        );
                    },
                ]);
            }
        }
        return Inertia::render('sezad/SezadDashboard', [
            'user' => $user,
        ]);
    }
}
