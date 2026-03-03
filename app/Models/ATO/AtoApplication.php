<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Locator\ApplicationModel;
use App\Models\ATO\AtoBusinessEnterpriseClassification;
use App\Models\ATO\AtoBusinessSectorClassification;
use App\Models\User;

class AtoApplication extends Model
{
    use HasFactory;

    protected $table = 'ato_applications';

    protected $fillable = [
        'application_id',
        'form_number',
        'application_date',
        'application_type',
        'business_structure',
        'file_uploaded',
        'trades_name',

        // business profile
        'parent_company',
        'taxpayer_name',
        'TIN',
        'price',
        'PrimaryLine',
        'SecondaryLine',
        'nature_of_contract',
        'pcic_primary_line',
        'pcic_secondary_line',
        'pcic_Primary_email',
        'pcic_Secondary_email',
        'pcic_location',
        'pcic_office_address',
        'pcic_contact_person',
        'pcic_contact_number',

        'user_id',

        // 🔹 ADD THIS if not yet added in DB
        'ato_business_enterprise_classification_id',
        'ato_business_sector_classification_id',
    ];

    protected $casts = [
        'application_date' => 'date',
    ];

    /**
     * Link to locator application
     */
    public function applicationForm()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

    /**
     * Uploads
     */
    public function uploads()
    {
        return $this->hasMany(
            \App\Models\Locator\Upload::class,
            'application_form_id',
            'application_id'
        );
    }

    /**
     * ONE → Business Enterprise Classification
     */
    public function businessEnterpriseClassification()
    {
        return $this->belongsTo(
            AtoBusinessEnterpriseClassification::class,
            'ato_business_enterprise_classification_id', // foreign key in this table
            'id' // primary key in parent table
        );
    }

    public function businessSectorClassification()
    {
        return $this->belongsTo(
            AtoBusinessSectorClassification::class,
            'ato_business_sector_classification_id',
            'id'
        );
    }
}

