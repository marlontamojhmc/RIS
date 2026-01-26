<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;

class AtoPricing extends Model
{
    protected $table = 'ato_pricing';

    protected $fillable = [
        'application_type', // New | Renewal
        'min_area',
        'max_area',
        'price',
    ];
    public static function getPrice(string $type, int $area): ?float
{
    return self::where('application_type', $type)
        ->where('min_area', '<=', $area)
        ->where(function ($query) use ($area) {
            $query->where('max_area', '>=', $area)
                  ->orWhereNull('max_area');
        })
        ->value('price');
}
}
