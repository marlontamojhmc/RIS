<?php
namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PdfHelper
{
    public static function savePdf(string $view, array $data, string $filename, string $folder = 'pdfs')
    {
        // Ensure folder exists
        $path = storage_path("app/{$folder}");
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        // Generate PDF
        $pdf = Pdf::loadView($view, $data)->setPaper('A4', 'portrait');

        // Save PDF to storage/app/pdfs
        Storage::put("{$folder}/{$filename}", $pdf->output());

        return storage_path("app/{$folder}/{$filename}");
    }
}