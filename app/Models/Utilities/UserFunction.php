<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFunction extends Model
{
    use HasFactory;

    protected $table = 'user_function'; // or 'user_functions' if plural

    protected $fillable = [
        'department_id',
        'function',
    ];

    /**
     * Relationship: A user function belongs to one department
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Utilities\Department::class, 'department_id');
    }
}
