<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'department_id',
        'division_code',
        'division_name',
    ];
        public function department()
    {
        return $this->belongsTo(Department::class,'id');
    }
}
