<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accreditation\Accreditation;

class SupplyType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function accreditations()
    {
        return $this->belongsToMany(Accreditation::class, 'accreditation_supplies', 'supply_type_id', 'accreditation_id');
    }
}
