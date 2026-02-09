<?php

namespace App\Models\PERMIT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locator\ApplicationModel;
use App\Models\PERMIT\PermitClearanceFee;
use App\Models\Form; // make sure you import the Form model

class Permits extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'permits';

    // Fillable fields
    protected $fillable = [
        'form_id',
        'locator_name',
        'validity',
        'IS_number',
        'price',
        'delivery_date',
        'expiration_date',
        'form_number',
        'control_number',
        'application_id',
        'option_id',
    ];

    // Casts
    protected $casts = [
        'price' => 'decimal:2',
        'delivery_date' => 'date',
        'expiration_date' => 'date',
        'validity' => 'date',
    ];

    // Relationship to ApplicationModel
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }

    // Relationship to PermitClearanceFee
    public function option()
    {
        return $this->belongsTo(PermitClearanceFee::class, 'option_id');
    }

    // Relationship to Form
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
