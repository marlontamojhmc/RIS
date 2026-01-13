<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{

    protected $table = 'divisions';  
    protected $fillable = [
        'id',
        'division_code',
        'division_name',
        'department_id',
        'created_at',
        'updated_at'
    ];
}
