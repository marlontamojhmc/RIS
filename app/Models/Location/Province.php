<?php
namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'province';
    protected $fillable = ['code', 'name', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function municipality()
    {
        return $this->hasMany(Municipality::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }
}
