<?php

namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPSNBE extends Model
{
    use HasFactory;

    protected $table = 'spsnbe'; 
    protected $fillable = [
        'business_type_id', 'accreditation_type', 'price'
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}
