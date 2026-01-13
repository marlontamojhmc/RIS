<?php

namespace App\Http\Controllers\VENDOR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorController extends Controller
{
       public function index()
       {
            return Inertia::render('Vendor/Index',[]);
       
        }
}
