<?php
namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtoAccommodation extends Model
{
    use HasFactory;

    protected $table = 'ato_accommodation';

    protected $fillable = [
        'form_id',
        'application_id',
        'date_of_application',
        'registered_business_name',
        'application_type',
        'unit',
        'price',
        'location_of_units',
        'business_owner_name',
        'contact_numbers',
        'official_email',
        'authorized_representative_name',
        'authorized_representative_contact',
    ];


    public function caretakers()
    {
        return $this->hasMany(
            \App\Models\ATO\ATOCaretaker::class,
            'ato_accommodation_id'
        );
    }
}
