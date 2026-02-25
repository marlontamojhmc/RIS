<?php

namespace App\Http\Controllers\FINANCE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\User;
use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApplicationForApproval;
use App\Models\ApproverGroup;
use App\Models\Accreditation\Accreditation;
use App\Models\ATO\AtoApplication;
use App\Http\Requests\PaymentRequest;
use App\Services\AppService;

class FinanceController extends Controller
{
    protected $appService;
    public function __construct(AppService $appService)
    {
        $this->appService = $appService;
    }
    public function index(AppService $appService)
    {
        $applications = $appService->getApplicationsForApprover(auth()->id());

        return Inertia::render('FSD/FINANCE/Index', [
            'applications' => $applications
        ]);
    }
    
    public function show(Request $request)
    {  
       
      
    if($form_type === 'Permit'){
    $applications = ApplicationModel::with('permits')->findOrFail($id); 
    return Inertia::render('FSD/FINANCE/Permit/Show', [
        'applications'=> $applications,
    ]);
    }elseif($form_type === 'Accreditation'){
        $applications = ApplicationModel::with('accreditations')->findOrFail($id);
        return Inertia::render('FSD/FINANCE/Accreditation/Show2',[
            'applications'=> $applications,
        ]);
        //form_id # 8 is Enterprise Primary/Secondary form_type ATO
        //form_id # 9 is Accommodation Provider form_type ATO
    }elseif($form_type === 'ATO' ){
        $applications = ApplicationModel::with('atoApplication')->findOrFail($id);
        
        return Inertia::render('FSD/FINANCE/ATO/Show',[
            'applications' =>$applications,
        ]);
    }else{
        $applications= ApplicationModel::with('provisionalGrant')->findOrFail($id);
        return Inertia::render('FSD/FINANCE/Provisional/Show2',[
            'applications' =>$applications,
        ]);
    }
    $user = auth()->user();
    // if($form_type === 'Accreditation'){
    // $application = ApplicationModel::with(['accreditation','accreditation.supplies','accreditation.services','accreditation.businessEnterpriseClassifications','accreditation.uploads'])
    //                 ->find($id);
    // $accreditation = Accreditation::with(['supplies','services','businessEnterpriseClassifications','uploads'])->where('application_id',$id)->first();
                   
    // return Inertia::render('FSD/FINANCE/Accreditation/Show',[
    //                     'application' => $application,
    //                     'accreditation' => $accreditation,
    //                 ]);
                    
    // }elseif($form_type === 'Provisional'){
    //     $application = ApplicationModel::with(['articleDetails','approval', 'uploads', 'selections','options','provisionalGrant'])
    //                 ->find($id);
    // }else{
    //     $application = ApplicationModel::with(['articleDetails','approval', 'uploads', 'selections','options'])
    //                 ->find($id);
    // }
   
    // if (!$application) {
    //     abort(404, 'Application not found');
    // }

    // if (!$application->approval) {
    //     abort(404, 'Approval not found');
    // }
//     $atoapp = AtoApplication::where('application_id',$id)->first();
//     $approver = ApproverGroupApprover::where('approver_group_id', $application->approval->approver_group_id)
//                   ->where('approver_id', $user->id)
//                   ->where('application_form_id', $application->id)
//                   ->first();
   
//     if (!$approver || !$approver->status) {
//         abort(404, 'Approver not found or no status yet');
//     }
// $prevApprover = ApproverGroupApprover::where('approver_group_id', $application->approval->approver_group_id)
//                   ->where('sequence', ($approver->sequence - 1))
//                   ->where('application_form_id', $application->id)
//                   ->first();
   
//     $group = ApproverGroup::find($application->approval->approver_group_id);
    
    
        // 'application' => $application,
        // 'approver_status' => $approver->status,
        // 'group' => $group,
        // 'Prevapprover' => $prevApprover,
        // 'price' =>$atoapp?->price,
        
    
}
public function payment(PaymentRequest $request)
{
    $user = auth()->user();
    $validated = $request->validated();

    $applicationForApproval = ApplicationForApproval::where(
        'application_id',
        $validated['application_forms_id']
    )->first();

    if (!$applicationForApproval) {
        return response()->json([
            'success' => false,
            'message' => 'Application not found'
        ], 404);
    }

    $applicationForApproval->update([
        'IS_Number' => $validated['is_number'] ?? null,
        'payment_status' => 'Paid',
    ]);

    $approver = ApproverGroupApprover::where(
        'application_form_id',
        $validated['application_forms_id']
    )
    ->where('approver_id', $user->id)
    ->first();

    if ($approver) {
        $approver->update([
            'status' => 'Approved'
        ]);
    }

    return response()->json([
        'success' => true,
        'status' => 'Paid',
        'approver_id' => $user->id
    ]);
}

}
