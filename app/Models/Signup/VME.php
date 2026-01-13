<?php

namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VME extends Model
{
    use HasFactory;

    protected $table = 'vme'; 

    protected $fillable = [
        'business_type_id', 'accreditation_type', 'price'
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}
