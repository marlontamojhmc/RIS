<?php

namespace App\Http\Controllers\FINANCE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\User;
use App\Models\Locator\ApplicationModel;
use App\Models\ApproverGroup;
use App\Models\Accreditation\Accreditation;
use App\Models\ATO\AtoApplication;

class FinanceController extends Controller
{
    
    public function index(){
        $user = auth()->user();
    
                $applications = ApproverGroupApprover::with('application','application.accreditations')
                                ->where('approver_id', auth()->id())
                                ->orderBy('id', 'desc')
                                ->get();
        return Inertia::render('FSD/FINANCE/Index',[
                                'applications'=> $applications,
                                ]);
    }
    
    public function show(Request $request)
    {  
        dd($request->all());
      
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
    }elseif($form_type === 'ATO'){
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

}
