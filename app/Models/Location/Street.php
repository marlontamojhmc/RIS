<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    protected $table = 'street';
     protected $fillable = ['street_name', 'barangay_id'];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }
}
