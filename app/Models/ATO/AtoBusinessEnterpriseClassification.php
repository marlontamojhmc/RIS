<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtoBusinessEnterpriseClassification extends Model
{
    use HasFactory;

    protected $table = 'Ato_business_enterprise_classifications';

    protected $fillable = [
        'name',
        'description',
    ];

    public function atoApplications()
    {
        return $this->hasMany(
            AtoApplication::class,
            'ato_business_enterprise_classification_id', // foreign key in child table
            'id');
    }
}
