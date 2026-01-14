<?php

namespace App\Models\Accreditation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accreditation\Accreditation;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function accreditations()
    {
        return $this->belongsToMany(Accreditation::class, 'accreditation_services', 'service_type_id', 'accreditation_id');
    }
}
