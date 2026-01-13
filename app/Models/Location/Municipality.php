<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipality';
    protected $fillable = ['code', 'name', 'province_id'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function barangay()
    {
        return $this->hasMany(Barangay::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }
}
