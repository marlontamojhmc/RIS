<?php

namespace App\Http\Controllers\PERMITS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
       dd($form['table']);
    }
}
