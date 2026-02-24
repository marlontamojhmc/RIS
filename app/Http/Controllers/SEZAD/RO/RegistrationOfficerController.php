<?php

namespace App\Http\Controllers\SEZAD\RO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AppService;
class RegistrationOfficerController extends Controller
{
public function index(AppService $appService)
{
    $applications = $appService->getApplicationsForApprover(auth()->id());

    return Inertia::render('sezad/RO/Index', [
        'applications' => $applications
    ]);
}
}
