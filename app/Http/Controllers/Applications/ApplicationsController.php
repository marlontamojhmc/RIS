<?php

namespace App\Http\Controllers\Applications;

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
use Illuminate\Support\Facades\Auth;
use App\Models\ATO\AtoApplication;
use App\Services\AppService;
use Illuminate\Support\Facades\Gate;
use App\Models\Signup\TemporaryUser;
use App\Models\Signup\SignupApprover;

class ApplicationsController extends Controller
{
    protected $service;
    public function __construct(AppService $service)
{
    $this->service = $service;
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    { abort(403);
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
                'user_name'   => $selection->user->name,
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

    {   $user = auth()->user();
        //check if user has approved ATO if it does remove ATO to the Option
        $formOptions = $this->service->getFormOptionsForUser();

    
    return Inertia::render('Locator/Application/Create', [
                                            'user' => $user,
                                            'application_form_id' => null,
                                            'form' => $formOptions,
                                            'approverGroupId' => null,
        
                             ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {    
        $user = auth()->user();
        $result = $this->service->createApplication($request->type);
    
        $application = $result['application'];
        $approverForm = $result['approverForm'];

        if ($request->type === 'ATO') {
            return Inertia::render('ATO/Create2', [
                'application_id' => $application->id,
                'approver_group_id' => $approverForm->approver_group_id,
                'application_form_number' => $application->form_number
            ]);
        }else{ return Inertia::render('Locator/Application/Create', [ 
            'user' => $user, 
            'application_form_id' => $application->id, // Pass the new ID 
            'control_number' => $application->control_number, 
            'form_number' => $application->form_number, 
            'form_title' => $application->form_title,
            'start_date' => $application->created_at,
            'options' => ApplicationOption::select('id', 'name', 'value', 'validity')->get(),
            'approverGroupId' => $approverForm->approver_group_id, 
            ]); 
    }
}

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
       $data = $this->service->getApplicationData($id);
       $applicationForApproval = ApplicationForApproval::with('approverGroup.approvers')
                            ->where('application_id', $id)
                            ->first();
       $approver = ApproverGroupApprover::with(['approver', 'approverGroup'])
                            ->where('approver_group_id', $data['approverGroup']->id)
                            ->where('application_form_id', $id)
                            ->get(['id', 'approver_id', 'sequence', 'role','remark', 'status', 'acted_at', 'approver_group_id']);
        
            if($data['approverGroup']->allApproversStatusApproved()){
                $data['application']->status= AppConstants::STATUS_APPROVED;
                $data['application']->save();
                $applicationForApproval->status = AppConstants::STATUS_APPROVED;
                $applicationForApproval->save();
            }   
    
        return Inertia::render('Locator/Application/Show', [
        'application' => $data['application'],
        'approverGroup' => $data['approverGroup'],
        'approvers' => $data['approvers']->map(function ($item) {
           
            return [
                'id' => $item['id'] ?? null,
                'name' => $item['name'] ?? '(Unknown)',
                'email' => $item['email'] ?? null,
                'pivot' => [
                    'role' => $item['pivot']['role'] ?? null,
                    'sequence' => $item['pivot']['sequence'] ?? null,
                    'status' => $item['pivot']['status'] ?? null,
                    'acted_at' => $item['pivot']['acted_at'] ?? null,
                    'remark' => $item['pivot']['remark'] ?? null,
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
    public function pendingList()
    {
            $ato = ApplicationModel::where('user_id', Auth::id())->where('form_title', 'ATO')->get();
            $appForm_number = ApplicationModel::where('user_id', Auth::id())->pluck('form_number');
            $applications = ApplicationForApproval::with([
                                    'application',
                                    'approverGroup.approvers'
                                ])
                                ->whereIn('form_number', $appForm_number)
                                ->where('status', 'Pending')
                                ->get();
         return Inertia::render('Locator/Application/Pending', [
                            'applications' => $applications,
                            'ATO' => $ato,
        ]);
    }

    public function approvedList()
    {
            $appIds = ApplicationModel::where('user_id', Auth::id())->pluck('id');
            $applications = ApplicationForApproval::with([
                            'application',
                            'approverGroup.approvers'
                            ])
                            ->whereIn('application_id', $appIds)
                            ->where('status', 'Approved')
                            ->get();

            return Inertia::render('Locator/Application/Approved', [
                'applications' => $applications,
            ]);
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
