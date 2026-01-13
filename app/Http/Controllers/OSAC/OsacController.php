<?php
namespace App\Http\Controllers\OSAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApproverGroupApprover;
use Illuminate\Support\Facades\Gate;
use App\Models\Locator\ApplicationModel;
use App\Models\ApproverGroup;
class OsacController extends Controller
{
   
   public function index()
   { 
      $user= auth()->user();
         $applications = ApproverGroupApprover::with('application')
                     ->where('approver_id', auth()->id())
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
   {
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
}
