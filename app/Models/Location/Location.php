<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    protected $table = 'location';  
    protected $fillable = [
        'id',
        'region_id',
        'province_id',
        'municipality_id',
        'barangay_id',  
        'created_at',
        'updated_at'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }
}
