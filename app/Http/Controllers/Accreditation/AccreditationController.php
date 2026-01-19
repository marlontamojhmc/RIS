<?php

namespace App\Http\Controllers\Accreditation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use Inertia\Inertia;

class AccreditationController extends Controller
{
    /**
     * Return dynamic form options
     */
    public function options()
    {
        return response()->json([
            'services' => ServiceType::all(),
            'supplies' => SupplyType::all(),
            'frequencies' => Frequency::all(),
        ]);
    }

    /**
     * Store new Accreditation
     */

public function store(Request $request){
   dd($request->all());
        // Validate input
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:new,renewal',
            'businessName' => 'required|string|max:255',
            'frequency' => 'required|string|max:50',
            'services' => 'array',
            'supplies' => 'array',
            'address' => 'required|string',
            'email' => 'required|email',
            'contact' => 'nullable|string|max:50',
            'representative' => 'nullable|string|max:150',
            'privacyConsent' => 'required|boolean',
        ]);

        // Create accreditation tied to the authenticated user
        $accreditation = Accreditation::create([
            'user_id' => auth()->id(),
            'form_number'=>$request->form_number,
            'date' => $validated['date'],
            'type' => $validated['type'],
            'business_name' => $validated['businessName'],
            'frequency' => $validated['frequency'],
            'address' => $validated['address'],
            'email' => $validated['email'],
            'contact' => $validated['contact'] ?? null,
            'representative' => $validated['representative'] ?? null,
            'privacy_consent' => $validated['privacyConsent'],
        ]);

        // Optionally attach services and supplies if you have pivot tables
        if (!empty($validated['services'])) {
            $accreditation->services()->sync($validated['services']);
        }
        if (!empty($validated['supplies'])) {
            $accreditation->supplies()->sync($validated['supplies']);
        }
       $forApproval = new ApplicationForApproval(); 

    $forApproval->application_id = $request->application_id;
    $forApproval->approver_group_id = $request->application_group_id;
    $forApproval->form_number = $request->form_number;
    $forApproval->status = 'Pending';
    $forApproval->save();

        
        // Return a redirect with success message via Inertia
       return response(['success' => true, 'message' => 'Accreditation submitted successfully!']);
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
