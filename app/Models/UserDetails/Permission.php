<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    protected $table = 'permissions';  
    protected $fillable = [
        'id',
        'name',
        'role_id',
        'created_at',
        'updated_at'
    ];
}
