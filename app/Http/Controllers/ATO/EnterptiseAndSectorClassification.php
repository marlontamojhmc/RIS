<?php

namespace App\Http\Controllers\ATO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ATO\AtoBusinessEnterpriseClassification;
use App\Models\ATO\AtoBusinessSectorClassification;
use App\Models\ATO\AtoPricing;
use App\Models\ATO\AccommodationUnit;
use App\Models\Signup\BusinessType;

class EnterptiseAndSectorClassification extends Controller
{
    public function options()
    {
        return response()->json([
            'Enterprise' => AtoBusinessEnterpriseClassification::where('type','Bussiness')->get(),
            'Sector' => AtoBusinessSectorClassification::all(),
            'pricing' =>AtoPricing::all(),
            'unit' =>AccommodationUnit::all(),
            'businessTypes' => BusinessType::all(),
            
        ]);
    }

}
