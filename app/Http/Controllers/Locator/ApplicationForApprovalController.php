<?php

namespace App\Http\Controllers\Locator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locator\ApplicationForApproval;
use App\Models\Locator\ApplicationModel;
use Inertia\Inertia;
use App\Helpers\PermitHelper;
use App\Models\ApproverGroup;
use App\Models\Locator\ApproverGroupApprover;
use Illuminate\Support\Facades\DB;

class ApplicationForApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     * For Approver Group, Approver, ApplicationForApproval proof of concept
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $app = ApplicationModel::findOrFail($request->input('application_id'));
       
        ApplicationForApproval::create([
            'application_id'    => $request->input('application_id'),
            'approver_group_id' => $request->input('approver_group_id'),
            'form_number'       => $app->form_number,
            'status'            => 'Pending',
        ]);

        return Inertia::render('Locator/Application/Create', []);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $approvers = ApplicationForApproval::with('approverGroup.approvers')->find($id);
    
        // This is how to get the ApproverGroup
        echo "<pre>";
        print_r($approvers->approverGroup);

        // This is how to get the Approvers of the ApproverGroup
        print_r($approvers->approverGroup->approvers);
        echo "</pre>";
    }

    /**
     * Approve the application for the given approver.
     */
   public function approve(Request $request, $formNumber, $approverId)
{  
    
    $appForm = ApplicationModel::where('form_number', $formNumber)->firstOrFail();
    $forApprovaAfterIS = ApplicationForApproval::where('form_number', $formNumber)->firstOrFail();

    if($request->user == 'finance'){
            $appForm->control_number = PermitHelper::controlNumberGenerate();
            $appForm->save();
            $forApprovaAfterIS->payment_status = 'Paid';
            $forApprovaAfterIS->acted_at = now();
            $forApprovaAfterIS->save();

        }
    $applicationForApproval = ApplicationForApproval::where('application_id', $appForm->id)
        ->with('approverGroup.approvers')
        ->firstOrFail();
    
    $group = $applicationForApproval->approverGroup;
    
    $approverMember = ApproverGroupApprover::where('approver_id',$approverId)
             ->where('application_form_id', $appForm->id)
             ->where('approver_group_id', $group->id)
             ->first();
        $approverMember->status ='Approved';
        $approverMember->acted_at = now();
        $approverMember->save();
        
        
      $remaining = ApproverGroupApprover::pending()
                ->where('approver_group_id',$group->id)
                ->where('application_form_id', $appForm->id)
                ->count();

           if ($remaining === 0) {
                $applicationForApproval->status='Approved';
                $applicationForApproval->acted_at =now();
                $applicationForApproval->save();

                $appForm->status = 'Approved';
                $appForm->save();
            }
    // ✅ Return updated data to frontend
    return back()->with([
        'success' => 'Approved successfully.',
        'application' => $appForm,
        'approvers' => $group->approvers,
    ]);
}


public function returnApproval(Request $request, $formNumber, $approverId)
{  
    $appForm= ApplicationModel::where('form_number', $formNumber)->first();

    $applicationForApproval = ApplicationForApproval::where('application_id', $appForm->id)
                            ->with('approverGroup')
                            ->firstOrFail();

    $group = $applicationForApproval->approverGroup;
    
     $approver = ApproverGroupApprover::where('approver_id', $approverId)
               ->where('application_form_id',$applicationForApproval->application_id)
               ->first();
          
                if ($approver) {
                    $prevApprover = ApproverGroupApprover::where('approver_group_id', $approver->approver_group_id)
                        ->where('application_form_id', $applicationForApproval->application_id)
                        ->where('sequence', ($approver->sequence - 1))
                        ->first();
                
                    if ($prevApprover) {
                        $prevApprover->status = 'Pending';
                        $prevApprover->remark = $request->comment;
                        $prevApprover->save();
                       
                    } 
                }
             

     // Optionally mark the whole application as returned
    $applicationForApproval->update([
        'status'   => 'Returned',
        'remark'   => $request->comment,
        'acted_at' => now(),
    ]);

    return back()->with([
        'success' => 'Returned',
        'application' => $appForm,
        'approvers' => $group->approvers,
        'remark' =>$request->comment,
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
    $approval = ApplicationForApproval::findOrFail($id);

    $status = $request->input('status');

    if (in_array($status, ['approved', 'returned'])) {
        $approval->update([
            'status' => $status,
        ]);
    }

    return redirect()->back()->with('success', "Application has been {$status}.");
}
     /**
      * update form add the Invoice number
      */
     public function invoice(Request $request, $formNumber, $approverId)
    {   //value of invoice from frontend
        $forInvoice = ApplicationForApproval::where('form_number', $formNumber)->first();
        $forInvoice->IS_Number = $request->IS;
        $forInvoice->save();
       
        return back()->with([
        'success' => 'Approved Invoice Number successfully.',
    ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
