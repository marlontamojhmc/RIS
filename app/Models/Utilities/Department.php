<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'department_code',
        'department_name',
    ];
     public function divisions()
    {
        return $this->hasMany(Division::class,'department_id');
    }
     public function userFunctions()
    {
        return $this->hasMany(UserFunction::class,'department_id');
    }
}
