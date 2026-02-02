<?php

namespace App\Models\UserDetails;
use App\Models\User;
use App\Models\Signup\BusinessType;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
    ];

    public function userDetails()
    {
        return $this->hasMany(UserDetail::class, 'role_id');
    }
    public function users()
    {
    return $this->hasMany(User::class, 'role_id');
    }
    public function businessType()
{
    return $this->belongsTo(BusinessType::class, 'business_type_id');
}

}
