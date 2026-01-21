<?php

namespace App\Http\Controllers\ProvisionalGrant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProvisionalGrant\ProvisionalGrant;
use App\Models\Locator\Upload;
use App\Models\Locator\ApplicationForApproval;

class ProvisionalGrantController extends Controller
{
   

public function store(Request $request)
{   
    // map frontend field
    $request->merge([
        'classification_id' => $request->classification
    ]);

    $validated = $request->validate([
        'date' => 'required|date',
        'type' => 'required|in:new,renewal',

        'businessName' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'required|email',
        'contact' => 'nullable|string|max:50',
        'representative' => 'nullable|string|max:150',

        'classification_id' => 'required|exists:business_enterprise_classifications,id',

        'form_number' => 'required|string|unique:provisional_grants,form_number',
        'application_id' => 'required|integer',
        'application_group_id' => 'required|integer',

        'privacyConsent' => 'required|in:true,false,1,0',

        // ✅ file validation
        'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    // normalize boolean
    $validated['privacyConsent'] = filter_var(
        $validated['privacyConsent'],
        FILTER_VALIDATE_BOOLEAN
    );

    // ✅ create provisional grant
    if($request->form_type ==='ProvisionalGrant'){
    $grant = ProvisionalGrant::create([
        'date' => $validated['date'],
        'type' => $validated['type'],
        'business_name' => $validated['businessName'],
        'address' => $validated['address'],
        'email' => $validated['email'],
        'contact' => $validated['contact'] ?? null,
        'representative' => $validated['representative'] ?? null,
        'privacy_consent' => $validated['privacyConsent'],
        'classification_id' => $validated['classification_id'],
        'form_number' => $validated['form_number'],
        'application_id' => $validated['application_id'],
        'application_group_id' => $validated['application_group_id'],
    ]);
    
    // ✅ HANDLE FILE UPLOADS
    if ($request->hasFile('files')) {
    foreach ($request->file('files') as $file) {

        $path = $file->store('provisional_grants', 'public');

        Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => 'Provisional Grant Attachment',
            'user_id' => auth()->id(),
            'application_form_id' => $validated['application_id'],
        ]);
    }
}
    ApplicationForApproval::create([
        'application_id' => $validated['application_id'],
        'approver_group_id' =>$validated['application_group_id'],
        'form_number' => $validated['form_number'],
        'status' => 'Pending',
    ]);
    return response()->json([
        'success' => true,
        'message' => 'Provisional Grant submitted successfully',
        'data' => $grant->load('uploads'),
    ], 201);
}else{ 
    return;
}
}

public function all(){
    $grant = ProvisionalGrant::with('classification')->find(1);
    dd($grant->classification->price);
}
}
