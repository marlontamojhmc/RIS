<?php

namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TFBOSTA extends Model
{
    use HasFactory;

    protected $table = 'tfbosta'; 

    protected $fillable = [
        'business_type_id', 'accreditation_type', 'price','description'
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}
