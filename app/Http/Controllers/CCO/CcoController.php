<?php

namespace App\Http\Controllers\CCO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApplicationModel;
use App\Models\ApproverGroup;

class CcoController extends Controller
{   
    
    public function index(){
        $user = auth()->user();
                $applications = ApproverGroupApprover::with('application')
                                ->where('approver_id', auth()->id())
                                ->orderBy('id', 'desc')
                                ->get();

        return Inertia::render('sezad/CCO/Index',[
                                 'applications'=> $applications,
                                ]);
        }
    public function show($id){
        $user = auth()->user();
        $application = ApplicationModel::with(['articleDetails','approval', 'uploads', 'selections','options'])
                        ->where('id', $id)
                        ->first();
        $approver = ApproverGroupApprover::where('approver_group_id', $application->approval->approver_group_id)
                  ->where('approver_id', $user->id)
                  ->where('application_form_id', $application->id)
                  ->first();
        $group = ApproverGroup::where('id', $application->approval->approver_group_id)->first();
        $prev_approver = ApproverGroupApprover::where('approver_group_id', $application->approval->approver_group_id)
    ->where('sequence', ($approver->sequence - 1))
    ->where('application_form_id', $application->id)
    ->first();

       return Inertia::render('sezad/CCO/Show',[
        'application' => $application,
        'approver_status' => $approver->status,
        'group' => $group,
        'prev_approver' => $prev_approver->status,
       ]);
    }
}
