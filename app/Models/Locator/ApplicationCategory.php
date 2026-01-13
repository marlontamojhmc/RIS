<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationCategory extends Model
{
    use HasFactory;

    protected $table = 'application_categories'; 
    protected $fillable = [
        'name',
    ];

    public function options()
    {
        return $this->hasMany(ApplicationOption::class, 'category_id');
    }
}
