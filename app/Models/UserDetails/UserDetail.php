<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UserDetails\Role;
use App\Models\Signup\BusinessType;


class UserDetail extends Model
{

    protected $table = 'user_details';  
    protected $fillable = [
        'id',
        'user_id',
        'employee_id',
        'email',
        'status',
        'is_active',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'position_id',        
        'department_id',   
        'division_id',
        'role_id',
        'permission_id',
        //'birth_date',
        'location_id',
        'sex',
        'user_function_id',
        'business_type_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function businessType(){
        return $this->belongsTo(BusinessType::class,'business_type_id');
    }
}
