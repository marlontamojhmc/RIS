<?php

namespace App\Http\Controllers\Signup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Signup\TemporaryUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\UserDetails\UserDetail;
use Ably\AblyRest;

class SignupController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            if ($user && method_exists($user, 'load')) {
                $user->load([
                    'details' => function ($query) {
                        $query->select(
                            'id',
                            'user_id',
                            'permission_id',
                            'role_id',
                            'department_id',
                            'division_id',
                            'user_function_id'
                        );
                    },
                ]);
            }
        }
        return Inertia::render('signup/Signup', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        try {
            // 1️⃣ Validate incoming request
            $validated = $request->validate([
                'email'             => 'required|email|unique:temporary_users,email',
                'businessName'      => 'required|string|max:255',
                'businessType'      => 'required',
                'selectedLocators'  => 'nullable|array',
                'selectedLocators.*' => 'string',
            ]);

            // 2️⃣ Generate random 6-character temporary password
            $tempPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

            // 3️⃣ Create temporary user
            $tempUser = TemporaryUser::create([
                'email'           => $validated['email'],
                'name'            => $validated['businessName'],
                'business_type'   => $validated['businessType'],
                'locator'         => json_encode($validated['selectedLocators'] ?? []),
                'status'          => 'new',
                'remark'          => null,
                'temp_password'   => bcrypt($tempPassword),
            ]);
             
            Log::info('Temporary user created', [
                'id' => $tempUser->id,
                'email' => $tempUser->email,
                'tempPassword' => $tempPassword
            ]);

            // 4️⃣ Determine recipients
            $recipients = [];

            // if (empty($validated['selectedLocators'])) {
            //     // No locators chosen → all users in department 12
            //     $users = UserDetail::where('department_id', 12)->with('user')->get();
            //     foreach ($users as $userDetail) {
            //         if ($userDetail->user && $userDetail->user->email) {
            //             $recipients[] = $userDetail->user->email;
            //         }
            //     }
            // } else {
            //     // Locators chosen → send to each email in the array
            //     $recipients = $validated['selectedLocators'];
            // }

            // // Ensure recipients is always an array and remove null/empty emails
            // $recipients = array_filter($recipients ?? []);
            $sezadRecipients = [];

            $sezadUsers = UserDetail::where('department_id', 12)
                ->with('user')
                ->get();

            foreach ($sezadUsers as $userDetail) {
                if ($userDetail->user && $userDetail->user->email) {
                    $sezadRecipients[] = $userDetail->user->email;
                }
            }

            // Final recipients array
            $recipients = $sezadRecipients; // SEZAD always included

            // If locators exist → add them
            if (!empty($validated['selectedLocators'])) {
                foreach ($validated['selectedLocators'] as $locatorEmail) {
                    if (!empty($locatorEmail)) {
                        $recipients[] = $locatorEmail;
                    }
                }
            }

            // Remove duplicates and empty values
            $recipients = array_unique(array_filter($recipients));

            // 5️⃣ Send emails
            foreach ($recipients as $email) {
                try {
                    $htmlContent = "
                    <h2>New Temporary User Signup</h2>
                    <p><strong>Name:</strong> {$tempUser->name}<br>
                    <strong>Email:</strong> {$tempUser->email}</p>
                    <p><strong>Temporary Password:</strong> {$tempPassword}</p>
                    <p>Please click the button below to login and set your password:</p>
                    <a href='http://192.168.100.185/settings/password' 
                    style='display:inline-block; padding:10px 20px; background-color:#1D4ED8; color:white; border-radius:5px; text-decoration:none;'>
                    Change password 
                    </a>
                ";

                    Mail::html($htmlContent, function ($message) use ($email) {
                        $message->to($email)
                            ->subject('New Temporary User Signup');
                    });

                    Log::info('Signup email sent', ['to' => $email]);
                } catch (\Exception $mailException) {
                    Log::error('Failed to send signup email', [
                        'to' => $email,
                        'error' => $mailException->getMessage()
                    ]);
                }
            }

            // 6️⃣ Send Ably notification (once, outside the loop)
            $recipients = !empty($recipients) ? array_values($recipients) : [];

            // if (!empty($recipients)) {
            //     try {
            //         $ably = new AblyRest(config('ably.key'));
            //         $channel = $ably->channel(config('ably.channel'));

            //         $payload = [
            //             'message' => "New Temporary User Signup: {$tempUser->name} ({$tempPassword})",
            //             'recipients' => ['sezad_manager@gmail.com'], // array of emails
            //             'timestamp' => now()->toDateTimeString(),
            //         ];

            //         $channel->publish('signup', $payload);

            //         Log::info('Ably notification sent', ['payload' => $payload]);
            //     } catch (\Exception $ablyException) {
            //         Log::error('Failed to send Ably notification', ['error' => $ablyException->getMessage()]);
            //     }
            // }

            $ablyKey = env('ABLY_KEY');
            if (!$ablyKey) {
                return response()->json(['error' => 'Ably key not set'], 500);
            }

            $ably = new AblyRest($ablyKey);

            $channel = $ably->channel('notifications');
            $channel->publish('new_notification', [
                'title' => 'New Temporary User Signup',
                'message' => 'Temporary user: ',
                $tempUser->email
            ]);

            Log::info('New Notification', [
                'id' => $tempUser->id,
                'email' => $tempUser->email,
                'tempPassword' => $tempPassword
            ]);


            return response()->json([
                'message' => 'Signup successful.',
                'data' => $tempUser,
                'tempPassword' => $tempPassword
            ], 201);
        } catch (\Exception $e) {
            Log::error('Signup error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
