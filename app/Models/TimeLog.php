<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $fillable = [
        'app_form_id',
        'sequence',
        'status',
        'remarks',
        'acted_at',
        'user_id',
        'role',
        'tbl_name',
        'id_tbl',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'created_at' => 'datetime',
    ];
}
