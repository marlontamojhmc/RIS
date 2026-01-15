<?php

namespace App\Http\Controllers\SUPER;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class SuperAdminController extends Controller
{
   public function index()
{
    // paginate 10 users per page
    $paginator = User::select('id', 'name', 'email')->paginate(10)->withQueryString();

    return Inertia::render('SuperAdmin/Index', [
        // rows for the table
        'users' => $paginator->items(),
        // pagination metadata
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ],
    ]);
}



}
