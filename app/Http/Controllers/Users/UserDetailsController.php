<?php

namespace App\Http\Controllers\Users;

use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChangePasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\UserDetails\Position;
use App\Models\Location\{
    Region,
    Province,
    Municipality,
    Barangay,
    Street,
    Location
};
use App\Models\Utilities\UserFunction;
use App\Models\User;
use App\Helpers\UserHelper;

class UserDetailsController extends Controller
{
    public function index(Request $request)
    {

        $user = UserHelper::loadUserWithDetails();
        $status       = $request->input('status', null);
        $departmentId = $request->input('department_id', null);
        $divisionId   = $request->input('division_id', null);
        $offset       = $request->input('offset', 0);
        $limit        = $request->input('limit', 100);

        // Call stored procedure
        $users = DB::select("
            CALL GetUsersWithFullDetails(?, ?, ?, ?, ?)
        ", [
            $status,
            $departmentId,
            $divisionId,
            $offset,
            $limit
        ]);



        $userFunctions = UserFunction::all();

        // Return as JSON

        return Inertia::render('users/UsersDashboard', [
            'auth' => [
                'user' => $user,
            ],
            'users' => $users,
            'userFunctions' => $userFunctions,

        ]);
    }

    public function store(Request $request)
    {
        //dd($request);
        //dd($request->all());
        DB::beginTransaction();
        $validated = $request->validate([
            'employee_id'   => 'required|string|max:50',
            'email_address' => 'required|email|max:100',
            'first_name'    => 'required|string|max:100',
            'middle_name'   => 'nullable|string|max:100',
            'last_name'     => 'required|string|max:100',
            'suffix'        => 'nullable|string|max:20',
            'status'        => 'required|numeric|in:0,1',
            'department_id' => 'nullable|numeric',
            'division_id'   => 'nullable|numeric',
            'role_id'       => 'required|numeric',
            'permission_id' => 'required|numeric',
            'position'      => 'required|string|max:100',
            //'birth_date'    => 'required|date',
            'sex'           => 'required|string|in:Male,Female',
            'user_function_id' => 'nullable|exists:user_functions_id',
            //'phone'         => 'nullable|string|max:20',
            //'address'       => 'nullable|array',
        ]);
    
        
        try {
            $tempPassword = Str::random(8);

            $full_name = trim("{$validated['first_name']} {$validated['middle_name']} {$validated['last_name']}" .
                ($validated['suffix'] ? ", {$validated['suffix']}" : ""));

            $user = User::create([
                'name' => $full_name,
                'email' => $validated['email_address'],
                'password' => $tempPassword,
            ]);

            $position = Position::create([
                'position_name' => $validated['position'],
            ]);

            //$locationId = $this->storeLocation($request);

            $user->details()->create([
                'user_id'        => $user->id,
                'employee_id'    => $validated['employee_id'],
                'email'          => $validated['email_address'],
                'status'         => $validated['status'],
                'first_name'     => $validated['first_name'],
                'middle_name'    => $validated['middle_name'],
                'last_name'      => $validated['last_name'],
                'suffix'         => $validated['suffix'],
                'position_id'    => $position->id,
                'sex'            => $validated['sex'],
                'department_id'  => $validated['department_id'],
                'division_id'    => $validated['division_id'],
                'role_id'        => $validated['role_id'],
                'permission_id'  => $validated['permission_id'],
                'user_function_id' => $validated['user_function_id'],
                //'location_id'    => $locationId,
                //'birth_date'     => $validated['birth_date'],
                //'phone'          => $validated['phone'],
            ]);

            $this->sendChangePassword($full_name, $tempPassword, $validated['email_address']);
            Log::info("User info created", [
                'user_id' => $user->id,
                'email' => $user->email,
                'temp_password' => $tempPassword
            ]);
            DB::commit();

            return redirect()->back()->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Store user failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'User not created!');
        }
    }

    // public function storeLocation(Request $request)
    // {
    //     $address = $request->address;
    //     //dd($address['street']);
    //     DB::beginTransaction();
    //     try{
    //     $region = Region::create([
    //         'code' => json_decode($address['region'])->code,
    //         'name' => json_decode($address['region'])->name,
    //     ]);

    //     $province = Province::create([
    //         'code' => json_decode($address['province'])->code,
    //         'name' => json_decode($address['province'])->name,
    //         'region_id' => $region->id,
    //     ]);

    //     $municipality = Municipality::create([
    //         'code' => json_decode($address['municipality'])->code,
    //         'name' => json_decode($address['municipality'])->name,
    //         'province_id' => $province->id,
    //     ]);

    //     $barangay = Barangay::create([
    //         'code' => json_decode($address['barangay'])->code,
    //         'name' => json_decode($address['barangay'])->name,
    //         'municipality_id' => $municipality->id,
    //     ]);

    //     $street = Street::create([
    //         'street_name' => $address['street'],
    //         'barangay_id' => $barangay->id,
    //     ]);
    //     DB::commit();

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Address user failed: ' . $e->getMessage());
    //         return response('error', 500);
    //     }

    //     DB::beginTransaction();
    //     try{
    //     $location = Location::create([
    //         'region_id' => $region->id,
    //         'province_id' => $province->id,
    //         'municipality_id' => $municipality->id,
    //         'barangay_id' => $barangay->id,
    //         'street_id' => $street->id,
    //     ]);
    //     DB::commit();
    //      } catch (\Throwable $e) {
    //         DB::rollBack();
    //         Log::error('Location user failed: ' . $e->getMessage());
    //         return response('error', 500);
    //     }
    //     return $location->id;
    // }

    public function sendChangePassword($name, $tempPassword, $email)
    {
        $data = [
            'name' => $name,
            'temp_password' => $tempPassword,
            'change_password_link' => 'http://localhost/settings/password?temp_password=' . $tempPassword,
        ];

        try {
            Mail::to($email)->send(new ChangePasswordMail($data));
        } catch (\Exception $e) {
            Log::warning('Email failed to send: ' . $e->getMessage());
        }
    }
}
