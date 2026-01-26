<?php

namespace App\Http\Controllers\ATO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ATO\AtoBusinessEnterpriseClassification;
use App\Models\ATO\AtoBusinessSectorClassification;
use App\Models\ATO\AtoPricing;

class EnterptiseAndSectorClassification extends Controller
{
    public function options()
    {
        return response()->json([
            'Enterprise' => AtoBusinessEnterpriseClassification::all(),
            'Sector' => AtoBusinessSectorClassification::all(),
            'pricing' =>AtoPricing::all(),
            
        ]);
    }

}
