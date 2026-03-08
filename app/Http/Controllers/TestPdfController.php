<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Locator\ApplicationModel;
use Illuminate\Support\Facades\File;

class TestPdfController extends Controller
{
    /**
     * Simple test PDF
     */
    public function index()
    {
        $data = [
            'title'  => 'Gate Pass Clearance',
            'status' => 'Approved',
        ];

        $pdf = Pdf::loadView('pdf.test', $data)
                  ->setPaper('A4', 'portrait');

        return $pdf->download('test.pdf');
    }

    /**
     * Generate Gate Clearance PDF for a specific application
     */
    public function generate($id)
    {
        $application = ApplicationModel::with([
            'articleDetails',
            'uploads',
            'userAppSelection.feeOption',
            'approval'
        ])->findOrFail($id);

        // Ensure storage folder exists
        $folder = storage_path('app/pdfs');
        if (!File::exists($folder)) {
            File::makeDirectory($folder, 0755, true);
        }

        // Generate PDF and enable remote images (for logos)
        $pdf = Pdf::loadView('pdf.gate-clearance', [
            'application' => $application
        ])
        ->setPaper('A4', 'portrait')
        ->setOptions(['isRemoteEnabled' => true]);

        // Full path for saving
        $filePath = $folder . "/gatepass_{$application->id}.pdf";

        // Save PDF to disk
        $pdf->save($filePath);

        // Confirm file exists before downloading
        if (!File::exists($filePath)) {
            abort(500, 'PDF generation failed.');
        }

        // Return as download
        return response()->download($filePath);
    }
}