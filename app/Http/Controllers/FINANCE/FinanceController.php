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

class FinanceController extends Controller
{
     public function index(){
        $user = auth()->user();
    
                $applications = ApproverGroupApprover::with('application')
                                ->where('approver_id', auth()->id())
                                ->orderBy('id', 'desc')
                                ->get();
        return Inertia::render('FSD/FINANCE/Index',[
                                 'applications'=> $applications,
                                ]);
        }
    public function show($id)
{
    $user = auth()->user();
    $application = ApplicationModel::with(['articleDetails','approval', 'uploads', 'selections','options'])
                    ->find($id);

    if (!$application) {
        abort(404, 'Application not found');
    }

    if (!$application->approval) {
        abort(404, 'Approval not found');
    }

    $approver = ApproverGroupApprover::where('approver_group_id', $application->approval->approver_group_id)
                  ->where('approver_id', $user->id)
                  ->where('application_form_id', $application->id)
                  ->first();

    if (!$approver || !$approver->status) {
        abort(404, 'Approver not found or no status yet');
    }

    $group = ApproverGroup::find($application->approval->approver_group_id);

    return Inertia::render('FSD/FINANCE/Show', [
        'application' => $application,
        'approver_status' => $approver->status,
        'group' => $group,
    ]);
}

}
