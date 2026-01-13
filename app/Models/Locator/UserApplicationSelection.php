<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserApplicationSelection extends Model
{
    use HasFactory;

    protected $table = 'user_application_selections';

    protected $fillable = [
        'user_id',
        'application_id',
        'option_id',
        'Expired_at',
        'amount',
        'status', // e.g., pending, approved, rejected
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function option()
    {
        return $this->belongsTo(ApplicationOption::class, 'option_id');
    }
}
