<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Locator\Form;
use App\Models\Locator\Holiday;

class UtilController extends Controller
{
    public function Form(){
        $forms = Form::all();
        return Inertia::render('Util/Form',[
            'forms'=> $forms
        ]);

    }
    public function Holiday(){
        $holiday =Holiday::all();
        return Inertia::render('Util/Holiday',[
            'holidays' => $holiday,
        ]);
    }
}
