<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Models\Utilities\Department;
use App\Models\Utilities\Division;
use App\Models\Utilities\Role;
use App\Models\Utilities\Permission;

class LookupController extends Controller
{
public function departments()
{
    // Department with its divisions
    return response()->json(
        Department::with('divisions')->get()
    );
}

public function roles()
{
    // Role with its permission
    return response()->json(
        Role::with('permissions')->get()
    );
}

 public function permissions(){
        // Return all permissions as JSON
        return response()->json(Permission::all());
    }

}
