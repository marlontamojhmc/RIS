<?php

namespace App\Http\Controllers\Signup   ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Signup\TemporaryUser;
use Inertia\Inertia;

class TemporaryUserController extends Controller
{
    public function index()
    {
    $tempUsers = TemporaryUser::latest()->get();

    return inertia('sezad/sezadDashboard', [
        'usersTemp' => $tempUsers,   
    ]);
    }

    // Store a new temporary user
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:temporary_users,email',
            'name' => 'required|string|max:255',
        ]);

        $user = TemporaryUser::create($request->all());

        return response()->json($user, 201);
    }

    // Optional: delete a temporary user
    public function destroy($id)
    {
        $user = TemporaryUser::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
