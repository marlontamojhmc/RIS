<?php

namespace App\Http\Middleware;

use App\Models\ApproverSets;
use Illuminate\Http\Request;
use Illuminate\Foundation\Inspiring;
use Inertia\Middleware;
use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApproverGroupApprover;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDetails\UserDetail;
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
    [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

    $user = $request->user();

//    dd($user);
if($user){

    $id = auth()->id();
    $urole = UserDetail::with('role')->where('user_id',$id)->first();
    $userRole = ApproverSets::where('user_id', $user->id)
    ->get(['approver_group_id','role','sequence']);
    }
    return array_merge(parent::share($request), [
         'app' => [
            'name' => config('app.name'),
            'quote' => [
                'message' => trim($message),
            ],
        ],

        'auth' => [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'userRole' => $urole->role->name,
                'role'=>  $userRole,
                'details' =>$urole,
                'count_notification'=> $user->notifications->count(),
            ] : null,
        ],

    ]);
}
}
