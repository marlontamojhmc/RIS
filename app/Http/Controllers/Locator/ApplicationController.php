<?php

namespace App\Http\Controllers\Locator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApplicationOption;
use Inertia\Inertia;
use App\Models\ApplicationCategory;
use App\Models\Locator\UserApplicationSelection;
use App\Helpers\PermitHelper;
use App\Models\User;
use App\Models\Locator\Form;
use App\Models\ApproverGroup;
use App\Models\ApproverSets;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\Locator\ApplicationForApproval;
use App\Helpers\AppConstants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
     { 

        
        $application = ApplicationModel::with(['selections.option', 'selections.user'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['message' => 'No application found'], 404);
        }
        
        // Format the selections
        $selections = $application->selections->map(function($selection) {
            $amount = $selection->amount;
            return [
                'option_name' => $selection->option->name,
                'amount'      => $selection->amount,
                'validity'    => $selection->option->validity,
                'selected_at' => $selection->selected_at,
                'user_name'   => $selection->user->name, // or email/id
            ];
        });
        $totalAmount = $application->selections->sum('amount');
        
       
        return response()->json([
            'application' => [
                'id'           => $application->id,
                'form_title'   => $application->form_title,
                'control_no'   => $application->control_number,
                'form_number'  => $application->form_number,
                'selections'   => $selections,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{   
    
    $form = Form::all();
     $user = auth()->user();
    return Inertia::render('Locator/Application/Create', [
        'user' => $user,
        'application_form_id' => null,
        'form' => $form,
        'approverGroupId' => null,
        
    ]);
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {  
       
         $user = auth()->user();
       $application = ApplicationModel::create([
            'form_title' => $request->input('type') ,
            'user_id'    => $user->id,
        ]);
        
        $approver = Form::where('name',$request->input('type'))->first();
        $sets = ApproverSets::where('approver_group_id',$approver->approver_group_id)->get();
        
        //create new Sets of ApprovergroupApprover 
        foreach ($sets as $set) {
            //set the Role of reciever that needs to recieve emails
            //it depends if admin wants to send email or notifications to all approver sets members
            // if($set->role === 'Manager'){
            //    $reciever = User::where('id',$set->user_id)->first();
            //    Log::info('Send Email to:', ['user Email: ' => $reciever->email ,"id" =>$reciever->id]); 
            // }
    ApproverGroupApprover::query()->insert([
    'approver_group_id' => $set->approver_group_id,
    'approver_id'       => $set->user_id,
    'sequence'          => $set->sequence,
    'role'              => $set->role,
    'application_form_id'=> $application->id,
    'status'            => AppConstants::STATUS_PENDING,
    'created_at'        => now(),
    'updated_at'        => now(),
]);
   
}
      
    // Collection of all options
    return Inertia::render('Locator/Application/Create', [
        'user' => $user,
        'application_form_id' => $application->id, // Pass the new ID
        'control_number' => $application->control_number,
        'form_number' => $application->form_number,
        'form_title' => $application->form_title,
        'start_date' => $application->created_at,
    'options' => ApplicationOption::select('id', 'name', 'value', 'validity')->get(),
    'approverGroupId' => $approver->approver_group_id,

    ]);

    }
    


    /**
     * Display the specified resource.
     */
        public function show(String $id)
{    
    $application = ApplicationModel::with(['articleDetails', 'uploads', 'selections'])
                   ->findOrFail($id);
    $approvers = ApplicationForApproval::with('approverGroup.approvers')
                    ->where('application_id', $id)
                    ->first();
    
    $approver = ApproverGroupApprover::with(['approver', 'approverGroup'])
    ->where('approver_group_id', $approvers->approverGroup->id)
    ->where('application_form_id', $id)
    ->get(['id', 'approver_id', 'sequence', 'role','remark', 'status', 'acted_at', 'approver_group_id']);
     
     if($approvers->approverGroup->allApproversStatusApproved()){
          $application->status= AppConstants::STATUS_APPROVED;
          $application->save();
          $approvers->status = AppConstants::STATUS_APPROVED;
          $approvers->save();
     }   
    
    return Inertia::render('Locator/Application/Show', [
    'application' => $application,
    'approverGroup' => $approvers?->approverGroup,
    'approvers' => $approver->map(function ($item) {
        return [
            'id' => $item->approver->id ?? null,
            'name' => $item->approver->name ?? '(Unknown)',
            'email' => $item->approver->email ?? null,
            'pivot' => [
                'role' => $item->role ?? null,
                'sequence' => $item->sequence ?? null,
                'status' => $item->status ?? null,
                'acted_at' => $item->acted_at ?? null,
                'remark' => $item->remark ?? null,
            ],
        ];
    }),
]);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function pendingList(){
        return Inertia::render('Locator/Application/Pending', []);
    }
    public function approvedList(){
        return Inertia::render('Locator/Application/Approved',[]);
    }
    /**
     * Request $request are application_id and option_id selected by the locator
     * this function saves selected option to user_application_selected Table
     * 
     * **/
    public function saveOptionSelection(Request $request)
    { 
    $user = auth()->user();
    $validated = $request->validate([
        'application_id' => 'required|exists:application_forms,id',
        'option_id' => 'required|exists:application_options,id',
    ]);
    $validity= ApplicationOption::find($validated['option_id'])->value ?? null;  
    $application = ApplicationModel::findOrFail($validated['application_id'])->get();
    $expireddate= PermitHelper::computeValidity((int)$validity);
    
    $form = Form::where('name', $application[0]->form_title)->get();

    $price = ApplicationOption::find($validated['option_id'])->price;
    $selection = UserApplicationSelection::updateOrCreate(
        [
            'application_id' => $validated['application_id'],
            'user_id'        => auth()->id(),
        ],
        [
            'option_id'   => $validated['option_id'],
            'Expired_at'  => PermitHelper::computeValidity((int)$validity),
            'selected_at' => now(),
            'amount'      => $price,
        ]
    );
    
    
    return Inertia::render('Locator/Application/Create',[
       'user' => $user,
        'application_form_id' => $validated['application_id'],
        'options' => ApplicationOption::select('id', 'name', 'value', 'validity')->get(),
        'expired_at'=> $expireddate,
        'form_number' =>$application[0]->form_number,
        'control_number' =>$application[0]->control_number,
        'form_title' => $application[0]->form_title,
        'start_date' => $application[0]->created_at,
        'price' => $price,
        'approverGroupId' => $form[0]->approver_group_id,
        
        
    ]);
    
    }
}
