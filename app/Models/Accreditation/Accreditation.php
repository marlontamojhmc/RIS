<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accreditation\ServiceType;
use App\Models\Accreditation\SupplyType;

class Accreditation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'type',
        'business_name',
        'frequency',
        'address',
        'email',
        'contact',
        'representative',
        'privacy_consent',
    ];

    // Many-to-Many relationships with services and supplies
    public function services()
    {
        return $this->belongsToMany(ServiceType::class, 'accreditation_services', 'accreditation_id', 'service_type_id');
    }

    public function supplies()
    {
        return $this->belongsToMany(SupplyType::class, 'accreditation_supplies', 'accreditation_id', 'supply_type_id');
    }
}
