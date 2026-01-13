<?php
namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locator\ApplicationModel;

class ApplicationMeta extends Model
{
    use HasFactory;

    protected $table = 'application_meta';
    public $incrementing = false; // no auto-increment
    protected $primaryKey = null; // no primary key
    public $timestamps = false;

    protected $fillable = [
        'application_form_id',
        'meta_key',
        'meta_value',
        
    ];
   
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'application_form_id');
    }
}
