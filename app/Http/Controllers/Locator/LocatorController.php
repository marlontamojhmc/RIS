<?php

namespace App\Http\Controllers\Locator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApplicationModel;
use Illuminate\Support\Facades\Auth;
use App\Models\Locator\ApplicationForApproval;
use App\Helpers\AppConstants;
use App\Models\Locator\ApproverGroupApprover;
use App\Models\User;
use App\Models\ATO\AtoApplication;
use Illuminate\Support\Facades\Gate;
use App\Models\Signup\TemporaryUser;
use Illuminate\Support\Facades\Log;
use App\Models\UserDetails\UserDetail;
use App\Models\Signup\SignupApprover;


class LocatorController extends Controller
{
    public function index(){
        
         $user = auth()->user();
    
  if (Gate::denies('access-locator')) {
            abort(403, 'Unauthorized');
        }
       
       $AppForapprovals = $user->approvals;
       
         $applications = auth()->user()?->applications ?? [];
        return Inertia::render('Locator/Index', [
            'applications' => $AppForapprovals,
        ]);
    }
    
    public function show(String $id){


       
       $application = ApplicationModel::with(['articleDetails', 'uploads', 'selections'])
    ->where('id', $id)
    ->first();
    
       return Inertia::render('Locator/View',[
        'app' => $application,
       ]);
    }
    public function serviceProviderRequest()
    { 
        $user = auth()->user();
         $status= "new";
        $tempUsers = TemporaryUser::where('locator', 'like','%'. $user->email .'%')
                                    ->where('business_type', 1)                
                                    ->where('status','like','%'.$status.'%')
                                    ->get();
                      
                return Inertia::render('Locator/ServiceProvider/ServiceProviderRequest',[
                   'serviceProvider' => $tempUsers,  
                ]);
    }
    
    public function approveServiceProviderRequest($id)
    { $user = auth()->user();
        $serviceprovider =  TemporaryUser::findOrFail($id);
        if (User::where('email', $serviceprovider->email)->exists()) {
    abort(422, 'Email already exists');
}      
      $newServiceProviderUser = User::create([
        'name' => $serviceprovider->name,
        'email' => $serviceprovider->email,
        'password' => $serviceprovider->temp_password,
        'created_at'=> now(),// date of vendor was verified
        'updated_at' =>now(),
      ]);
      $userDetails = UserDetail::create([
        'user_id' => $newServiceProviderUser->id,
        'email' => $newServiceProviderUser->email,
        'status' => 1,
        'first_name'=> $newServiceProviderUser->name,
        'role_id' => 4,
        'permission_id' => 2,
        'created_at'=> now(),
        'upddated_at' =>now(),
        
    ]);
   
    $serviceprovider->status ='approved';
    $serviceprovider->save();
     }
 

    public function MyServiceProviders()
    { 
        $user = auth()->user();
        $tempUsers = TemporaryUser::where('locator', 'like', '%' . $user->email . '%')
        ->where('status','like','%approved%')
        ->where('business_type', 1)
        ->get();
         $tempUsers = TemporaryUser::query()
    ->where('locator', 'like', '%' . $user->email . '%')
    ->where('status', 'new')
    ->where('business_type', 1)
    ->whereDoesntHave('signupApprovers', function ($q) use ($user) {
        $q->where('approver_id', $user->id);
    })
    ->get();
        return Inertia::render('Locator/ServiceProvider/MyServiceProvider',[
                   'serviceProvider' => $tempUsers,  
                ]);
    }

    
    public function myVendors()
    {
        $user = auth()->user();
        
        $myvendors = SignupApprover::with('temporary_user') 
        ->where('approver_id', $user->id)
        ->where('status', 'Approved')
        ->get();
         
        return Inertia::render('Locator/Vendor/MyVendor', [
              'vendor' => $myvendors,
         ]); 
      
    }
    public function approveVendorRequest($id, Request $request)
    {  
    $user = auth()->user();
    $vendor = TemporaryUser::findOrFail($id);
    $request->validate([
    'status' => 'required|in:Approved,Rejected',
    'remark' => 'required|string|min:3',
     ]);
     SignupApprover::create([
    'temporary_user_id' => $vendor->id,
    'approver_id' => auth()->id(),
    'status' => $request->status,
    'remark' => $request->remark,
    'approved_at' => now(),
     ]); 
       Log::info($vendor->email.' was Aprroved by: '.$user->email);     
    }
     public function vendorRequest()
    {
    $user = auth()->user();
    // compare SignupApprovers table to Tempusers table if locator had been already approved the vendor and get all new status that is requested
    //by the vendor
    $tempUsers = TemporaryUser::query()
    ->where('locator', 'like', '%' . $user->email . '%')
    ->where('status', 'new')
    ->where('business_type', 4)
    ->whereDoesntHave('signupApprovers', function ($q) use ($user) {
        $q->where('approver_id', $user->id);
    })
    ->get();
         
            return Inertia::render('Locator/Vendor/VendorRequest',[
            'vendor' => $tempUsers,
            
            ]);
    }
    
}

