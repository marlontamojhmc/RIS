<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $table = 'uploads';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'user_id',
        'application_form_id',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applicationForm()
    {
        return $this->belongsTo(\App\Models\Locator\ApplicationModel::class, 'application_form_id');
    }
}
