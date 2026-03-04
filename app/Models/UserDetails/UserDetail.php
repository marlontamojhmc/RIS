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
    public function isSezadManager(): bool
{
    return
        $this->department_id === 12 &&
        $this->role_id === 2 &&
        $this->permission_id === 1 &&
        $this->position_id === 50 &&
        $this->user_function_id === 5;
}
 public function isOsac():bool 
 {
    return $this->department_id === 12 &&
        $this->role_id === 2 &&
        $this->permission_id === 2 &&
        $this->position_id === 36 &&
        $this->user_function_id === 4;
 }
 public function isCCO(): bool 
 {
    return $this->department_id === 12 &&
        $this->role_id === 2 &&
        $this->permission_id === 2 &&
        $this->position_id === 37 &&
        $this->user_function_id === 8;
 }
 public function isRO(): bool 
 {
    return $this->department_id === 12 &&
        $this->role_id === 2 &&
        $this->permission_id === 2 &&
        $this->position_id === 60 &&
        $this->user_function_id === 7;
 }
 public function isFinance():bool 
 {
    return $this->department_id === 10 &&
        $this->role_id === 2 &&
        $this->permission_id === 2 &&
        $this->position_id === 53 &&
        $this->user_function_id === 8;
 }
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
