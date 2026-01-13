<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ApproverGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * 🔗 Relationship: Group has many approvers (many-to-many)
     */
    public function approvers()
{
    return $this->belongsToMany(
        User::class,
        'approver_group_approver', // ✅ actual table
        'approver_group_id',
        'approver_id'
    )
    ->withPivot('sequence', 'status', 'role', 'application_form_id','remark')
    ->orderBy('sequence', 'asc')
    ->withTimestamps();
}

    /**
     * ✅ Check if all approvers have approved
     */
    public function allApproversStatusApproved(): bool
    {    
        return $this->approvers->every(fn($a) => $a->pivot->status === 'Approved');
    }
    public function approverStatusApproved($approverId): bool
{
    return $this->approvers()
        ->wherePivot('approver_group_id', $this->id)
        ->wherePivot('approver_id', $approverId)
        ->wherePivot('status', 'Approved')
        ->exists();
}
}
