<?php

namespace App\Models\PERMIT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locator\ApplicationModel;//application_forms table
use App\Models\Locator\Form;//Forms table

class PermitClearanceFee extends Model
{
    use HasFactory;

    protected $table = 'permit_clearance_fees';

    protected $fillable = [
        'form_id',
        'code',
        'title',
        'description',
        'value',
        'validity',
        'price',
    ];

    protected $casts = [
        'validity' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * A permit clearance fee belongs to an application form
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
