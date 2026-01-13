<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UserHelper
{
    public static function loadUserWithDetails()
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User) { {
            }
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
        return $user;
    }
}
