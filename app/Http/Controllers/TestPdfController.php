<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Locator\ApplicationModel;
use App\Helpers\PermitHelper;
class TestPdfController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Gate Pass Clearance',
            'status' => 'Approved'
        ];

        $pdf = Pdf::loadView('pdf.test', $data);

        return $pdf->download('test.pdf');
        // return $pdf->stream('test.pdf');
    }
    public function generate($id){
        
           $application = ApplicationModel::with(['articleDetails', 'uploads', 'selections','options'])
    ->where('id', $id)
    ->first();
     return view('pdf.gate-clearance', compact('application'));
    // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.gate-clearance', [
    //     'application' => $application
    // ]);
    
     //return $pdf->stream('gate-clearance.pdf');
    
    }
}
