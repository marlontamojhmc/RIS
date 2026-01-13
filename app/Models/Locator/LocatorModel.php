<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocatorModel extends Model
{
    use HasFactory;

    protected $table = 'locator_profile';

    protected $fillable = [
        'user_id',
        'locator_name',
        'owner_contact_number',
        'representative_name',
        'representative_email',
        'applied_date',
        'applicant_signature',
        'address_within_jhze',
        'company_email',
        'owner_name',
        'owner_email',
        'representative_contact',
        'official_email_gmail',
        'applicant_name',
        'type_of_industry',
        'company_mobile_number',
        'category',
    ];

    /**
     * Relation: Locator belongsTo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
