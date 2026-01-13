<?php

namespace App\Http\Controllers\VENDOR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocatorVendorController extends Controller
{
    public function index(){
        return Inertia::render('Vendor/IndexLoc',[]);
    }
}
