<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Optional: if you want to relate it to Accreditation
    public function accreditations()
    {
        return $this->hasMany(Accreditation::class, 'frequency_id'); 
        // use frequency_id as FK in accreditations table if you normalize
    }
}
