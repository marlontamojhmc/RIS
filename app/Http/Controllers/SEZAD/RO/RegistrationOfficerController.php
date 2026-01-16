<?php

namespace App\Http\Controllers\SEZAD\RO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationOfficerController extends Controller
{
    public function Index(){
          return Inertia::render('sezad/RO/Index',[]);
    }
}
