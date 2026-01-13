<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Locator\ApplicationModel;
class AtoApplication extends Model
{
    use HasFactory;

    protected $table = 'ato_applications'; // Change if needed

    protected $fillable = [
        'application_id',
        'application_date',
        'application_type',
         'business_structure',//sole,corporation etc
        'file_uploaded',
        'trades_name',
        //business profile
        'parent_company',
        'taxpayer_name',
        'TIN',
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
    ];

    protected $casts = [
        'application_date' => 'date',
    ];
    public function applicationForm()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }
     public function uploads()
    {
        return $this->hasMany(\App\Models\Locator\Upload::class, 'application_form_id', 'application_id');
    }

}
