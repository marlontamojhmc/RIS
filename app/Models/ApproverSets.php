<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Locator\ApproverGroup;

class ApproverSets extends Model
{
    use HasFactory;

    protected $table = 'approver_sets'; // your actual table name

    protected $fillable = [
        'approver_group_id',
        'user_id',
        'role',
        'sequence',
    ];

    /**
     * 🔗 Each assignment belongs to one approver group.
     */
    public function approverGroup()
    {
        return $this->belongsTo(ApproverGroup::class, 'approver_group_id');
    }

    /**
     * 🔗 Each assignment belongs to one user (the approver).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
