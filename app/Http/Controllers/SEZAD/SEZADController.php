<?php

namespace App\Http\Controllers\SEZAD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Signup\TemporaryUser;
use App\Models\Signup\BusinessType;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SEZADController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $businessTypes = BusinessType::all();
        $tempUsers = TemporaryUser::latest()->get();

        if ($user instanceof User && method_exists($user, 'load')) {
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

        return Inertia::render('sezad/SezadDashboard', [
            'user' => $user,
            'usersTemp' => $tempUsers,
            'businessTypes' => $businessTypes,
        ]);
    }

    public function updateTempUser(Request $request)
    {
        // 1️⃣ Validate request
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|string', // approved/disapproved
            'remark' => 'required|string'
        ]);

        // 2️⃣ Get temp user
        $tempUser = TemporaryUser::findOrFail($request->id);
        
        Log::info('Updating Temporary User', [
            $tempUser
            
        ]);
        // 3️⃣ Decrypt temp password if exists
        $tempPassword = $tempUser->temp_password ? decrypt($tempUser->temp_password) : null;

        Log::info('Temporary password retrieved', [
            'user_id' => $tempUser->id,
            'email' => $tempUser->email,
            'temp_password' => $tempPassword,
        ]);

        // 4️⃣ Update temp user status and remark
        $tempUser->status = $request->status;
        $tempUser->remark = $request->remark;
        $tempUser->save();

        // 5️⃣ If approved, move to main users + details table
        if ($request->status === 'approved' || $request->status === 'Approved') {
            DB::beginTransaction();
            try {
                // Generate temporary password if not present
                if (!$tempPassword) {
                    $tempPassword = Str::random(8);
                }

                // Create main User
                
                $user = User::create([
                    'name' => $tempUser->name,
                    'email' => $tempUser->email,
                    'password' => bcrypt($tempPassword),
                ]);

                $user->details()->create([
                    'email'         => $tempUser->email,
                    'first_name'    => $tempUser->name,       // FULL NAME HERE
                     'last_name' => ' ',
                    'status'        => 1,               // ACTIVE
                    'role_id'       => $tempUser->business_type+3, // BUSINESS TYPE AS ROLE
                    'department_id' => 12,              // Optional: assign default DEPT
                    'division_id'   => null,            // Optional fields
                    'permission_id' => null,
                    'position_id'   => null,
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Failed to create user from temp user: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user',
                ], 500);
            }
        }

        // 6️⃣ Prepare email content
        $statusLabel = ucfirst($request->status);
        $buttonHtml = '';
        $tempPasswordHtml = '';

        if ($request->status === 'approved' || $request->status === 'Approved') {
            $tempPasswordHtml = "<p><strong>Temporary Password:</strong> {$tempPassword}</p>";
            $buttonHtml = "
                <a href='http://192.168.100.185/settings/password' 
                   style='display:inline-block; padding:10px 20px; background-color:#1D4ED8; color:white; border-radius:5px; text-decoration:none;'>
                   Set Your Password
                </a>
            ";
        }

        $htmlContent = "
            <h2>Account {$statusLabel}</h2>
            <p><strong>Name:</strong> {$tempUser->name}<br>
            <strong>Email:</strong> {$tempUser->email}</p>
            {$tempPasswordHtml}
            <p><strong>Remark:</strong> {$tempUser->remark}</p>
            {$buttonHtml}
        ";

        try {
            Mail::html($htmlContent, function ($message) use ($tempUser) {
                $message->to($tempUser->email)
                    ->subject('Your Temporary Account Status');
            });
            Log::info("Approval email sent to {$tempUser->email}");
            Log::info("Message in the email {$htmlContent}");
        } catch (\Exception $mailException) {
            Log::error('Failed to send approval email: ' . $mailException->getMessage());
        }

        // 7️⃣ Return JSON
        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'temp_user' => $tempUser,
            'user_created' => $request->status === 'approved' ? true : false,
            'user' => $user ?? null,
        ]);
    }
}
