<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $table = 'barangay';
    protected $fillable = ['code', 'name', 'municipality_id'];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function street()
    {
        return $this->hasMany(Street::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }
}

