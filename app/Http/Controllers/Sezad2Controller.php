<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Locator\ApproverGroupApprover;
use Inertia\Inertia;

class Sezad2Controller extends Controller
{
    public function index ($id)
    {   
        $user = auth()->user();

        $applications = ApproverGroupApprover::with([
            'application',
            'application.accreditations',
            'application.user',
            'application.ApproverGroupApprovers.approver',
            'application.articleDetails',
            'application.selections',
            'application.uploads',
        ])
        ->where('approver_id', $user->id)
        ->whereHas('application', function ($query) {
            $query->whereIn('form_type', ['Permit', 'Accreditation', 'ATO']);
        })
        ->orderByDesc('id')
        ->get();
        $newId = 0;
        $newId += $id;
         return  Inertia::render('Sezad2/Index',
               ['applications'=>$applications,
               'request'=> $newId,
               'status'=>'Approved',
               ]
         );
        dd();
    }
    public function create(){
      

     return Inertia::render('Sezad2/Index',[]);
    }
}