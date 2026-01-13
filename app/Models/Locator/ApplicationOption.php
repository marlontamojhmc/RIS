<?php
namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationOption extends Model
{
    use HasFactory;

    protected $table = 'application_options';

    protected $fillable = [
        'name',
        'value',
        'validity',
        'Expired_at',
    ];

   
}
