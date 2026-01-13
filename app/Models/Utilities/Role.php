<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',        
        'name',
    ];

   public function permissions()
    {
        return $this->hasMany(Permission::class, 'role_id');
    }
}
