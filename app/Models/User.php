<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Locator\ApplicationModel;
use App\Models\Locator\ApplicationForApproval;
use App\Models\UserDetails\UserDetail;
use App\Models\UserDetails\Department;
use App\Models\UserDetails\Division;
use App\Models\UserDetails\Role;
use App\Models\UserDetails\Permission;
use App\Models\UserDetails\Position;
use App\Models\UserDetails\Location;
use App\Models\Locator\LocatorModel;
use App\Models\ATO\AtoApplication;
use App\Models\UserDetails\UserMeta;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permission_id',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function applications()
    {
    return $this->hasMany(ApplicationModel::class, 'user_id');
    }
    public function approverGroups()
    {
    return $this->belongsToMany(\App\Models\ApproverGroup::class, 'approver_group_approver', 'approver_id', 'approver_group_id')
                ->withPivot('sequence')
                ->orderBy('pivot_sequence', 'asc');
    }
    public function approverGroup()
{
    return $this->belongsToMany(
        \App\Models\ApproverGroup::class,
        'approver_group_approver',
        'approver_id',
        'approver_group_id'
    )
    ->withPivot('sequence')
    ->orderBy('pivot_sequence', 'asc');
}
    public function approvals()
{
    return $this->hasManyThrough(
        ApplicationForApproval::class, 
        ApplicationModel::class,       
        'user_id',                     
        'application_id',              // Foreign key on ApplicationForApproval
        'id',                          // Local key on User
        'id'                           // Local key on ApplicationModel
    );
}


    public function details()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function role()
     {
    return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsTo(Permission::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function profile(){
        return $this->hasOne(LocatorModel::class);
    }
    public function atoApplication()  // singular
{
    return $this->hasOne(AtoApplication::class, 'user_id', 'id');
}
//=========UserMeta=============================//
   public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }
    public function getMeta($key, $default = null)
{
    return $this->meta()
        ->where('meta_key', $key)
        ->value('meta_value') ?? $default;
}

public function setMeta($key, $value)
{
    return $this->meta()->updateOrCreate(
        ['meta_key' => $key],
        ['meta_value' => $value]
    );
}
}
