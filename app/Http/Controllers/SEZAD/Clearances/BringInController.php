<?php

namespace App\Http\Controllers\SEZAD\Clearances;

use Illuminate\Http\Request;
//use App\Models\BringInClearanceTask;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class BringInController extends Controller
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

        return Inertia::render('sezad/clearances/BringIn', [
            'user' => $user,
        ]);
    }
}

