<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PERMIT\PermitClearanceFee;

class UserApplicationSelection extends Model
{
    use HasFactory;

    protected $table = 'user_application_selections';

    protected $fillable = [
        'user_id',
        'application_id',
        'option_id',
        'Expired_at',
        'selected_at',
        'amount',
       
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function application()
    // {
    //     return $this->belongsTo(Application::class);
    // }

    public function application()
{
    return $this->belongsTo(ApplicationModel::class, 'application_id');
}

    public function feeOption()
    {
    return $this->belongsTo(PermitClearanceFee::class,'option_id');
    }
}
