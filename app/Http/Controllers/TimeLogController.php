<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeLogController extends Controller
{
    public function index()
{
    // Fetch all records
    $data = \App\Models\TimeLog::all();

    // If using Inertia:
    return inertia('sezad/index', [
        'items' => $data
    ]);
}
}
