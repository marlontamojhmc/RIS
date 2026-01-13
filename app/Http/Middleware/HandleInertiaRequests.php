<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Inertia\Middleware;
use App\Models\Locator\ApplicationModel;
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

        // Only fetch applications if the user is authenticated
        $applications = $request->user()
    ? ApplicationModel::where('user_id', Auth::id())->where('form_title', 'ATO')->get()->toArray()
    : [];

        return array_merge(parent::share($request), [
            // 🌐 Global app data
            'app' => [
                'name' => config('app.name'),
                'quote' => [
                    'message' => trim($message),
                    'author'  => trim($author),
                ],
            ],

            // 👤 Authenticated user
            'auth' => [
                'user' => $request->user(),
            ],

            // 📂 Sidebar state
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') 
                || $request->cookie('sidebar_state') === 'true',

            // 💬 Flash messages
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'info'    => $request->session()->get('info'),
            ],

            // 📝 Applications
            'applications' => $applications,
           'user_details' => optional($request->user())->details ?? [],
        ]);
    }
}
