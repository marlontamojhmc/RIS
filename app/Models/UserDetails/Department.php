<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

    protected $table = 'departments';  
    protected $fillable = [
        'id',
        'department_code',
        'department_name',
        'created_at',
        'updated_at'
    ];
}
