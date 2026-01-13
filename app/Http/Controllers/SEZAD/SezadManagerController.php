<?php

namespace App\Http\Controllers\SEZAD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\User;
use App\Models\Locator\ApplicationModel;
use App\Models\ApproverGroup;

class SezadManagerController extends Controller
{
   public function index()
   {
      // if (Gate::denies('access-sezadManager')) {
      //          abort(403, 'Unauthorized');
      // }
               $applications = ApproverGroupApprover::with('application')
                        ->where('approver_id', auth()->id())
                        ->orderBy('id', 'desc')
                        ->get();
      return Inertia::render('sezad/Manager/Index',[
                                 'applications'=> $applications,
                              ]);

   }
    public function show($id)
    {
        $user = auth()->user();
      //    if (Gate::denies('access-sezadManager')) {
      //       abort(403, 'Unauthorized');
      //   }
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
       return Inertia::render('sezad/Manager/Show',[
                                          'application' => $application,
                                          'approver_status' => $approver->status,
                                          'group' => $group,
                                          'prev_approver' => $prev_approver->status,
                            ]);
    }
}
