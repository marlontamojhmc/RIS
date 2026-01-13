<?php

namespace App\Http\Controllers\Signup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocatorController extends Controller
{
    /**
     * Fetch all locators with role_id = 3.
     */
    public function index()
    {
        $locators = DB::table('users')
            ->join('user_details', 'users.id', '=', 'user_details.user_id')
            ->where('user_details.role_id', 3)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'user_details.role_id'
            )
            ->get();
             
        return response()->json($locators);
    }
}
