<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\ApproverGroup;
use App\Models\User;
use App\Models\Locator\ApplicationModel;

class ApproverGroupApprover extends Pivot
{
    protected $table = 'approver_group_approver';

    public $timestamps = true;

    protected $fillable = [
        'approver_group_id',
        'approver_id',
        'sequence',
        'status',
        'role',
        'remark',
        'application_form_id',
    ];

    /**
     * 🔗 The approver group this pivot belongs to
     */
    public function approverGroup()
    {
        return $this->belongsTo(ApproverGroup::class, 'approver_group_id');
    }

    /**
     * 🔗 The user who is the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    public function scopePending($query)
{
    return $query->where('status','!=' ,'Approved');
}
    public function application()
{
    return $this->belongsTo(ApplicationModel::class, 'application_form_id');
}
}
