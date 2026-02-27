<?php
namespace App\Http\Controllers\OSAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApproverGroupApprover;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApplicationModel;
use App\Models\ApproverGroup;
use App\Helpers\AppConstants;
class OsacController extends Controller
{
   
   public function index()
   { 
      $user= auth()->user();
      $applications = ApproverGroupApprover::with(
                                                   'application',
                                                   'application.accreditations',
                                                   'application.user',
                                                   'application.ApproverGroupApprovers.approver',
                                                   'application.articleDetails',
                                                   'application.selections',
                                                   'application.uploads'
                                                )
                                                ->where('approver_id', $user->id)
                                                ->whereHas('application', function ($query) {
                                                   $query->where('form_type', 'Permit');
                                                })
                                                ->orderBy('id', 'desc')
                                                ->get();
                     
      return Inertia::render('sezad/OSAC/Index',[
         'applications'=> $applications,
      ]);
    
   }
   public function create()
   {
      return Inertia::render('sezad/OSAC/Create',[]);
   }
   public function show($id)
   { dd($id);
      $user= auth()->user();
            $application = ApplicationModel::with(['articleDetails', 'uploads', 'options','selections','approval'])
                           ->where('id', $id)
                           ->first();
            $approver = ApproverGroupApprover::with(['approver'])->where('approver_group_id', $application->approval->approver_group_id)
                        ->where('approver_id', $user->id)
                        ->where('application_form_id', $application->id)
                        ->first();
            $group = ApproverGroup:: where('id', $application->approval->approver_group_id)->first();
         
         return Inertia::render('sezad/OSAC/Show',[
                                             'application' => $application,
                                             'approver_status' => $approver->status,
                                             'group' => $group,
                                ]);
   }
   public function Approve(Request $request)
   {
   $application_id = $request->app_id;
    $approver_group_id = $request->approver_group_id;
    $approver_sequence = $request->approver_sequence;
    $osacApprover = ApproverGroupApprover::where('approver_group_id',$approver_group_id)
             ->where('approver_id', auth()->id())
             ->where('application_form_id', $application_id)
             ->first();
   $osacApprover->status = AppConstants::STATUS_APPROVED;
   $osacApprover->save();

   }
   //
   public function Return(Request $request)
   {
    $application_id = $request->app_id;
    $approver_group_id = $request->approver_group_id;
    $approver_sequence = $request->approver_sequence;
    $osacApprover = ApproverGroupApprover::where('approver_group_id',$approver_group_id)
             ->where('approver_id', auth()->id())
             ->where('application_form_id', $application_id)
             ->first();
   $previousApprover= ApproverGroupApprover::where('approver_group_id',$approver_group_id)
            ->where('application_form_id',$application_id)
            ->where('sequence', ($osacApprover->sequence -1))
            ->first();
   $previousApprover->status = AppConstants::STATUS_PENDING;
   $previousApprover->remark = $request->remark;
   $previousApprover->acted_at = now();
   $previousApprover->save();

   $osacApprover->status = AppConstants::STATUS_PENDING;
   $osacApprover->acted_at = now();
   $osacApprover->save();
   }
   public function Index2(){
      return Inertia::render('sezad/OSAC/Index2',[]);
   }
   public function store2(Request $request)
{ $application= ApplicationModel::find($request->id);
       return Inertia::render('sezad/OSAC/Index2',[
         'application' => $application,
       ]);


    // process form
}
 public function PermitsPage(){
     $permits = ApplicationModel::with('approverGroupApprovers')
    ->where('form_type', 'Permit')
    ->whereHas('approverGroupApprovers', function ($query) {
        $query->where('approver_id', auth()->id())
              ->where('status', 'Pending');
    })
    ->get();
               dd($permits);
 return Inertia::render('sezad/OSAC/Pages/PermitsList',[]);
 }
 public function AccreditationPage(){
   $accreditation = ApplicationModel::with('approverGroupApprovers')
    ->where('form_type', 'Accreditation')
    ->whereHas('approverGroupApprovers', function ($query) {
        $query->where('approver_id', auth()->id())
              ->where('status', 'Pending');
    })
    ->get();
    dd($accreditation);
   return Inertia::render('sezad/OSAC/Pages/AccreditationList',[]);
 }
 public function ProvisionalGrantPage(){
   $provisionalGrant = ApplicationModel::with('approverGroupApprovers')
    ->where('form_type', 'ProvisionalGrant')
    ->whereHas('approverGroupApprovers', function ($query) {
        $query->where('approver_id', auth()->id())
              ->where('status', 'Pending');
    })
    
    ->get();
    dd($provisionalGrant);
   return Inertia::render('sezad/OSAC/Pages/ProvisionalGrantList',[]);
 }
}
