<?php

namespace App\Http\Controllers\PERMITS\LPC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\ApplicationForApproval;
use App\Models\PERMIT\Permits;
use App\Models\PERMIT\PermitClearanceFee;
use App\Helpers\PermitHelper;

class LocalPurchaseClearanceController extends Controller
{
    public function index()
    {
              return Inertia::render('PERMITS/LPC/LocalPurchaseClearance',[
                
            ]);
    }
    public function store(Request $request)
    {
       $form = json_decode($request->input('form'), true);
       $price = isset($form['amount']) 
        ? (float) str_replace(['₱', ','], '', $form['amount']) 
        : 0;
        $fee = PermitClearanceFee::find($form['selectedFeeId'] ?? 0);
        
        $validity =  PermitHelper::computeValidity((int)$fee->value);
        
        $gatepass = Permits::create([
            'form_id' =>$form['form_id'] ?? '',
        'locator_name' => $form['clearanceTo'] ?? '',
        'validity' => $validity->toDateTimeString(),
        'IS_number' => '',
        'price' => $price,
        'form_type' => 'Local-Purchase-Clearance',
        'delivery_date' => $form['deliveryDate'] ?? null,
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
        'message' => 'Local-Purchase-Clearance created successfully',
        'gatePass' => $gatepass,
    ]);
    }
}
