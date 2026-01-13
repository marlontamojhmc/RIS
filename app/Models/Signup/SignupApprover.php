<?php
  namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SignupApprover extends Model
{
    protected $fillable = [
        'temporary_user_id',
        'approver_id',
        'status',
        'remark',
        'approved_at',
    ];

    public function temporary_user()
{
    return $this->belongsTo(TemporaryUser::class, 'temporary_user_id');
}

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
