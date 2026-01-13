<?php
namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_form_id',
        'user_id',
        'marks_and_number',
        'qty',
        'detailed_description_of_article',
        'gross_weight',
    ];

    // ✅ Each article belongs to one application form
    public function application()
    {
        return $this->belongsTo(\App\Models\Locator\ApplicationModel::class, 'application_form_id');
    }

    // ✅ Each article also belongs to a user
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
