<?php

namespace App\Http\Controllers\PERMITS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApplicationForApproval;
use App\Models\PERMIT\GatePass;
use App\Models\PERMIT\PermitClearanceFee;
use App\Helpers\PermitHelper;
class GatePassController extends Controller
{
    public function index(){
       return Inertia::render('PERMIT/GatePass',[
         'application_id' => 1,
                      'approver_group_id' => 2,
                'application_form_number' => 3,
       ]);
    }
    public function store(Request $request){
       $form = json_decode($request->input('form'), true);

       $price = isset($form['amount']) 
        ? (float) str_replace(['₱', ','], '', $form['amount']) 
        : 0;
        $fee = PermitClearanceFee::find($form['selectedFeeId'] ?? 0);
        
        $validity =  PermitHelper::computeValidity((int)$fee->value);
        
        $gatepass = GatePass::create([
        'locator_name' => $form['clearanceTo'] ?? '',
        'validity' => $validity,
        'IS_number' => '',
        'price' => $price,
        'delivery_date' => $form['deliveryDate'] ?? null,
        'expiration_date' => $form['expirationDate'] ?? null,
        'form_number' => $form['gcNo'] ??  '',
        'control_number' => $form['controlNo'] ?? '',
        'application_id' => $form['application_form_id'] ?? null,
        'option_id' => $form['selectedFeeId'] ?? null,
    ]);
    ApplicationForApproval::create([
        'application_id' => $form['application_form_id'],
        'approver_group_id' => $form['approver_group_id'],
        'form_number' => $form['gcNo'],
        'status'=> 'Pending',
        'remark' => NULL,
        'IS_Number'=> NULL,
        'payment_status' =>'Pending',
        'acted_at' => NULL,
        'created_at' => now(),
        'updated_at' => now(),
        ]);

    return response()->json([
        'message' => 'Gate Pass created successfully',
        'gatePass' => $gatepass,
    ]);
    }
}
