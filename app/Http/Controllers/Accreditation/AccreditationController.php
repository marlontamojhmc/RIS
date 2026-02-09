<?php

namespace App\Http\Controllers\Accreditation;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Accreditation\BusinessEnterpriseClassifications;
use App\Models\Accreditation\Accreditation;
use App\Models\Accreditation\ServiceType;
use App\Models\Accreditation\SupplyType;
use App\Models\Accreditation\Frequency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Locator\ApplicationForApproval;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\Locator\Upload;
use App\Models\Locator\Form;
use Inertia\Inertia;

class AccreditationController extends Controller
{
    public function index()
{    $user = Auth()->user();
    $forms = Form::whereIn('id', [6, 12, 13])->get();
    
    return Inertia::render('Accreditation/Index', [
        'forms' => $forms,
        'user' => $user,
    ]);
}
    /**
     * Return dynamic form options
     */
    public function options()
    {
        return response()->json([
            'services' => ServiceType::all(),
            'supplies' => SupplyType::all(),
            'frequencies' => Frequency::all(),
            'classifications'=> BusinessEnterpriseClassifications::all(),
        ]);
    }

    /**
     * Store new Accreditation
     */

public function store(Request $request)
{  
    try {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:new,renewal',
            'businessName' => 'required|string|max:255',
            'frequency' => 'required|string|max:50',
            'services.*' => 'integer|exists:service_types,id',
            'supplies.*' => 'integer|exists:supply_types,id',
            'address' => 'required|string',
            'email' => 'required|email',
            'contact' => 'nullable|string|max:50',
            'representative' => 'nullable|string|max:150',

            // ✅ FIX boolean
            'privacyConsent' => 'required|in:true,false,1,0',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'errors' => $e->errors(),
        ], 422);
    }

    // Normalize boolean
    $validated['privacyConsent'] = filter_var(
        $validated['privacyConsent'],
        FILTER_VALIDATE_BOOLEAN
    );
    $price = $validated['type'] === 'new' ? 1000 : 500;
 $accreditationId = $request->application_id;
    $accreditation = Accreditation::create([
        'user_id' => auth()->id(),
        'form_number' => $request->form_number,
        'date' => $validated['date'],
        'type' => $validated['type'],
        'application_id' => $accreditationId,
        'price' =>$price,
        'business_name' => $validated['businessName'],
        'frequency' => $validated['frequency'],
        'address' => $validated['address'],
        'email' => $validated['email'],
        'contact' => $validated['contact'] ?? null,
        'representative' => $validated['representative'] ?? null,
        'privacy_consent' => $validated['privacyConsent'],
    ]);
   
    // ✅ Upload files (optional)
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            Upload::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $file->store('accreditations', 'public'),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'description' => 'Accreditation Attachment',
                'user_id' => auth()->id(),
                'application_form_id' => $accreditationId,
            ]);
        }
    }

    if (!empty($validated['services'])) {
        $accreditation->services()->sync($validated['services']);
    }

    if (!empty($validated['supplies'])) {
        $accreditation->supplies()->sync($validated['supplies']);
    }

    ApplicationForApproval::create([
        'application_id' => $accreditationId,
        'approver_group_id' => $request->application_group_id,
        'form_number' => $request->form_number,
        'status' => 'Pending',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Accreditation submitted successfully!',
    ], 200);
}
    public function show($id)
    {
       $forApproval = ApplicationForApproval::where('id', $id)
                        ->with('approverGroup.approvers')
                        ->first();
    
    //$forApproval->application_id
    $approvers = ApproverGroupApprover::where('application_form_id', $forApproval->application_id)->get();
   // dd($approvers);
       $accreditation = Accreditation::where('form_number', $forApproval->form_number)->first();
    
        return Inertia::render('Accreditation/AccreditationShow', [
            'accreditation' => $accreditation,
            'approvers' => $approvers,
        ]);
    }
    public function SupplierAccreditation(){
        return Inertia::render('Accreditation/Supplier/AccreditationForm',[]);
    }
    public function TradeFairAccreditation(){
        return Inertia::render('Accreditation/TradeFair/AccreditationForm',[]);
    }

}
