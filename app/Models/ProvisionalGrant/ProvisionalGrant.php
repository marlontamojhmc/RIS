<?php

namespace App\Models\ProvisionalGrant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Accreditation\BusinessEnterpriseClassifications;
use App\Models\Locator\Upload;

class ProvisionalGrant extends Model
{
    use HasFactory;

    protected $table = 'provisional_grants';

    protected $fillable = [
        'date',
        'type',
        'business_name',
        'address',
        'email',
        'contact',
        'representative',
        'privacy_consent',
        'classification_id',
        'form_number',
        'application_id',
        'application_group_id',
    ];

    protected $casts = [
        'date' => 'date',
        'privacy_consent' => 'boolean',
    ];

    /**
     * Relationships (optional)
     */
    public function classification()
    {
        return $this->belongsTo(BusinessEnterpriseClassifications::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applicationGroup()
    {
        return $this->belongsTo(ApplicationGroup::class);
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'application_form_id');
    }
}

