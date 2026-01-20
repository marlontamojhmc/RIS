<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Model;

class BusinessEnterpriseClassifications extends Model
{
    class BusinessEnterpriseClassification extends Model
{
    use HasFactory;

    protected $table = 'business_enterprise_classifications';

    protected $fillable = [
        'name',
    ];

    /**
     * A classification can belong to many accreditations
     */
    public function accreditations()
    {
        return $this->belongsToMany(
            Accreditation::class,
            'accreditation_business_enterprise_classification',
            'business_enterprise_classification_id',
            'accreditation_id'
        )->withTimestamps();
    }
}
}
