<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApproverGroup;
use App\Models\User;
use App\Models\Locator\ApplicationModel;

class ApplicationForApproval extends Model
{
    use HasFactory;

    protected $table = 'application_for_approval';

    protected $fillable = [
        'application_id',
        'approver_group_id',
        'approver_id',
        'form_number',
        'status',
        'remark',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    // 🔗 Relationship to the application (the actual request/transaction)
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }

    // 🔗 Relationship to the approver group
    public function approverGroup()
    {
        return $this->belongsTo(ApproverGroup::class, 'approver_group_id');
    }

    // 🔗 Relationship to the specific user approver (direct)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // ✅ Scope to get only applications assigned to a specific user (via their group)
    public function scopeAssignedToUser($query, $user)
    {
        $userId = is_object($user) ? $user->id : $user;

        return $query->whereHas('approverGroup.approvers', function ($q) use ($userId) {
            $q->where('approver_id', $userId);
        });
    }
}
