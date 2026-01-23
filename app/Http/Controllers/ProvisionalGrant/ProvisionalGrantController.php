<?php

namespace App\Http\Controllers\ProvisionalGrant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProvisionalGrant\ProvisionalGrant;
use App\Models\Locator\Upload;
use App\Models\Locator\ApplicationForApproval;
use Illuminate\Support\Facades\Log;
use App\Models\Accreditation\BusinessEnterpriseClassifications;

class ProvisionalGrantController extends Controller
{
    public function store(Request $request)
{    $Price = BusinessEnterpriseClassifications::find($request->classification);
    $request->merge([
        'classification_id' => $request->classification,
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
        'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    // Log validated data safely
    

    $validated['privacyConsent'] = filter_var($validated['privacyConsent'], FILTER_VALIDATE_BOOLEAN);

    $price = $Price->price;
    $grant = null;

    DB::transaction(function () use ($validated, $request, $price, &$grant) {

        $grant = ProvisionalGrant::create([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'price' => $price,
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

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                Upload::create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store('provisional_grants', 'public'),
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
            'approver_group_id' => $validated['application_group_id'],
            'form_number' => $validated['form_number'],
            'status' => 'Pending',
        ]);
    });

    // return redirect()
    //     ->route('fsd.finance.index')
    //     ->with('success', 'Provisional Grant submitted successfully');
}

    public function all()
    {
        $grant = ProvisionalGrant::with('classification')->findOrFail(1);

        return response()->json([
            'price' => $grant->classification->price,
        ]);
    }
}
