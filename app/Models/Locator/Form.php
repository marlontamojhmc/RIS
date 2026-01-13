<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locator\ApplicationModel;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'approver_group_id',
    ];

    /**
     * Relationship: A form belongs to one approver group.
     */
    public function approverGroup()
    {
        return $this->belongsTo(ApproverGroup::class, 'approver_group_id');
    }

    /**
     * Relationship: A form can have many applications submitted.
     */
    public function applications()
    {
        return $this->hasMany(ApplicationModel::class);
    }
}
