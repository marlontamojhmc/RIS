<?php

namespace App\Http\Controllers\PERMITS\BOC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApplicationForApproval;
use App\Models\PERMIT\Permits;
use App\Models\PERMIT\PermitClearanceFee;
use App\Helpers\PermitHelper;
use App\Services\UploadService;

class BringOutClearanceController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $service)
    {
        $this->uploadService = $service;
    }
    public function index()
    {

            return Inertia::render('PERMITS/BOC/BringOutClearance',[
                
            ]);
    }
    public function store(Request $request)
{  
    // Decode JSON form sent from Vue
    $form = json_decode($request->input('form'), true);
    
    // Handle file uploads via UploadService
    $uploadedFiles = [];
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $checklistItem => $file) {
            // UploadService can handle saving and returning path
            $uploadedFiles[$checklistItem] = $this->uploadService->uploadFile(
                $file,
                $checklistItem,
                $form['application_form_id'],
                auth()->id()
            );
        }
    }
   $allowedTypes = [
    'Gate Pass',
    'Bring In Clearance',
    'Bring Out Clearance',
    'Temporary Bring Out Clearance',
    'Local Purchase'
];
$formType = in_array($form['title'], $allowedTypes) ? $form['title'] : null;
    // Compute validity from selected fee
    $fee = isset($form['selectedFeeId']) ? PermitClearanceFee::find($form['selectedFeeId']) : null;
    $validity = $fee ? PermitHelper::computeValidity((int)$fee->value) : now();
    
    // Create Gate Pass
    $gatePass = Permits::create([
        'form_id' => $form['form_id'] ?? null,
        'locator_name' => $form['clearanceTo'] ?? '',
        'validity' => $validity->toDateTimeString(),
        'IS_number' => $form['siNumber'] ?? '',
        'price' => isset($form['amount']) ? (float) str_replace(['₱', ','], '', $form['amount']) : 0,
        'form_type' => $formType,
        'delivery_date' => $form['deliveryDate'] ?? null,
        'form_number' => $form['gcNo'] ?? '',
        'control_number' => $form['controlNo'] ?? '',
        'application_id' => $form['application_form_id'] ?? null,
        'option_id' => $form['selectedFeeId'] ?? null,
    ]);

    // Save ApplicationForApproval record
    ApplicationForApproval::create([
        'application_id' => $form['application_form_id'] ?? null,
        'approver_group_id' => $form['approver_group_id'] ?? null,
        'form_number' => $form['gcNo'] ?? '',
        'status'=> 'Pending',
        'remark' => null,
        'IS_Number'=> $form['siNumber'] ?? null,
        'payment_status' => 'Pending',
        'acted_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Return JSON response
    return response()->json([
        'message' => 'Gate Pass submitted successfully',
        'gatePass' => $gatePass,
        'uploadedFiles' => $uploadedFiles, // return uploaded file paths
    ]);
}
}
