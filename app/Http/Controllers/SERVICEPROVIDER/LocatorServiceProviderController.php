<?php

namespace App\Http\Controllers\SERVICEPROVIDER;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Signup\TemporaryUser;
use App\Models\Signup\BusinessType;

class LocatorServiceProviderController extends Controller
{
     public function index(){
         $user = auth()->user();

$TempUser = TemporaryUser::where('email', $user->email)
    ->with('businessType')
    ->firstOrFail();
    //dd($TempUser->businessType->name);
        return Inertia::render('ServiceProvider/Index',[
            'spsnbe' => $TempUser,
            ]);
     }
}
