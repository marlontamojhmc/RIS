<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{

    protected $table = 'positions';  
    protected $fillable = [
        'id',
        'position_name',
        'created_at',
        'updated_at'
    ];
}
