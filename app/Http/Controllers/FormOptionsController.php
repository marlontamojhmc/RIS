<?php
// app/Http/Controllers/FormOptionsController.php
namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\SupplyType;
use App\Models\Frequency;

class FormOptionsController extends Controller
{
    public function index()
    {
        return response()->json([
            'services' => ServiceType::all(),
            'supplies' => SupplyType::all(),
            'frequencies' => Frequency::all(),
        ]);
    }
}
