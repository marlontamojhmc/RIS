<?php

namespace App\Models\ATO;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApplicationModel;
class ATOCaretaker extends Model
{
    protected $table = 'Ato_caretaker';

    protected $fillable = [
        'application_id',
        'name',
        'role',
    ];

    /**
     * Caretaker belongs to an application
     */
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_id');
    }
}