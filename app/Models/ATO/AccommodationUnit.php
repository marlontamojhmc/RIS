<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;

class AccommodationUnit extends Model
{
    protected $table = 'accommodation_units';

    protected $fillable = [
        'label',
        'min_units',
        'max_units',
    ];

    /**
     * Check if a given unit count falls within this range
     */
    public function matchesUnits(int $units): bool
    {
        if ($this->max_units === null) {
            return $units >= $this->min_units;
        }

        return $units >= $this->min_units && $units <= $this->max_units;
    }
}
