<?php

namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvitionalGrant extends Model
{
    use HasFactory;

    protected $table = 'provitional_grant'; 

    protected $fillable = [
        'business_type_id', 'pg_type', 'description','price'
    ];

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }
}
