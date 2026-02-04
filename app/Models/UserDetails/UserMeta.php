<?php

namespace App\Models\UserDetails;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserMeta extends Model
{
   protected $table = 'usermeta';

    protected $fillable = [
        'user_id',
        'meta_key',
        'meta_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
