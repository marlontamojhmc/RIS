<?php

namespace App\Http\Middleware;

use App\Models\ApproverSets;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Inertia\Middleware;
use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApproverGroupApprover;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default with every Inertia response.
     */
    public function share(Request $request): array
    {
        // Split inspiring quote into message and author
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $user = Auth::user();
        // Only fetch applications if the user is authenticated
        $applications = //$request->user()
            ApproverGroupApprover::with('application',
                                                          'application.accreditations',
                                                          'application.user',
                                                          'application.ApproverGroupApprovers.approver',
                                                          'application.articleDetails',
                                                          'application.selections',
                                                          'application.uploads')
                                ->where('approver_id',$user->id)
                                ->orderBy('id', 'desc')
                                ->get();
    $userRole = ApproverSets::where('user_id', $user->id)->get(['approver_group_id','role','sequence']);
        return array_merge(parent::share($request), [
            // 🌐 Global app data
            'app' => [  
                'name' => config('app.name'),
                'quote' => [
                    'message' => trim($message),//
                ],
            ],

            // 👤 Authenticated user
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $userRole ?? null,

                    // add more fields if needed
                ] : null,
            ],

            // 📂 Sidebar state
            'sidebarOpen' => !$request->hasCookie('sidebar_state') 
                || $request->cookie('sidebar_state') === 'true',

            // 💬 Flash messages
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'info'    => $request->session()->get('info'),
            ],

            // 📝 Applications
            'applications' => $applications,

            // 🧾 User details (optional)
           'user_details' => $request->user()
    ? $request->user()
        ->load('details.businessType')
        ->details
    : null,
        ]);
    }
}
