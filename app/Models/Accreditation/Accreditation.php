<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accreditation\ServiceType;
use App\Models\Accreditation\SupplyType;
use App\Models\Locator\Upload;
class Accreditation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'form_number',
        'date',
        'type',
        'application_id',
        'business_name',
        'frequency',
        'address',
        'email',
        'contact',
        'representative',
        'privacy_consent',
    ];

     public function services()
    {
        return $this->belongsToMany(
            ServiceType::class,
            'accreditation_services',
            'accreditation_id',
            'service_type_id'
        );
    }

    // Supplies
    public function supplies()
    {
        return $this->belongsToMany(
            SupplyType::class,
            'accreditation_supplies',
            'accreditation_id',
            'supply_type_id'
        );
    }

    // Business Enterprise Classifications
    public function businessEnterpriseClassifications()
    {
        return $this->belongsToMany(
            \App\Models\BusinessEnterpriseClassification::class,
            'accreditation_business_enterprise_classification'
        );
    }

    // ✅ Uploads
    public function uploads()
    {
        return $this->hasMany(
            Upload::class,
            'application_form_id'
        );
    }
    
}
