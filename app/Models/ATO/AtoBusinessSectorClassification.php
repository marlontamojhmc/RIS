<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AtoBusinessSectorClassification extends Model
{
    use HasFactory;

    protected $table = 'Ato_business_sector_classifications';

    protected $fillable = [
        'name',
    ];

    /**
     * MANY sectors → MANY ATO applications
     */
    public function atoApplications()
    {
         return $this->hasMany(
            AtoApplication::class,
            'ato_business_sector_classification_id',
            'id'
        );
    }
}
