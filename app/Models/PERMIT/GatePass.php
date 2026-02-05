<?php

namespace App\Models\PERMIT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locator\ApplicationModel;
use App\Models\PERMIT\PermitClearanceFee;

class GatePass extends Model
{
    use HasFactory;

    // Specify table (optional if model name matches table)
    protected $table = 'gate_pass';

    // Fillable fields for mass assignment
    protected $fillable = [
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

    // Cast fields to proper types
    protected $casts = [
        'price' => 'decimal:2',
        'delivery_date' => 'date',
        'expiration_date' => 'date',
        'validity' => 'date',
    ];

    // Relationship to the application
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }

    // Relationship to the selected permit/fee
    public function option()
    {
        return $this->belongsTo(\App\Models\PERMIT\PermitClearanceFee::class, 'option_id');
    }
}
