<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'role_id',
    ];
     public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
