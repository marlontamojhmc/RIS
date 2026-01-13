<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'region'; 
    protected $fillable = ['code', 'name'];

    public function province()
    {
        return $this->hasMany(Province::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }
}
