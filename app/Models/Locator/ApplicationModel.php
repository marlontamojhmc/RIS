<?php

namespace App\Models\Locator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ATO\AtoApplication;

class ApplicationModel extends Model
{
    use HasFactory;

    protected $table = 'application_forms';

    protected $fillable = [
        'form_title',
        'user_id',
        'control_number',
        'form_number',
    ];

    protected static function booted()
    {
        static::creating(function ($application) {
            // ✅ Generate form_number (acronym + 4-digit sequence)
            $acronym = strtoupper(
                collect(explode(' ', $application->form_title))
                    ->map(fn($word) => mb_substr($word, 0, 1))
                    ->implode('')
            );

            $counter = 1;
            do {
                $formNumber = $acronym . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
                $exists = self::where('form_number', $formNumber)->exists();
                $counter++;
            } while ($exists);

            $application->form_number = $formNumber;
        });
    }

    /**
     * 🔗 Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articleDetails()
    {
        return $this->hasMany(\App\Models\Locator\ArticleDetail::class, 'application_form_id', 'id');
    }

    public function uploads()
    {
        return $this->hasMany(\App\Models\Locator\Upload::class, 'application_form_id', 'id');
    }

    public function selections()
    {
        return $this->hasMany(\App\Models\Locator\UserApplicationSelection::class, 'application_id', 'id');
    }

    /**
     * ✅ Relationship to approval (one record per application)
     */
    public function approval()
    {
        return $this->hasOne(\App\Models\Locator\ApplicationForApproval::class, 'application_id');
    }
    
    public function meta()
{
    return $this->hasMany(\App\Models\Locator\ApplicationMeta::class, 'application_id');
}
    public function setMeta(string $key, mixed $value): void
{
    // Convert objects or arrays to JSON before saving
    if (is_object($value) || is_array($value)) {
        $value = json_encode($value);
    }

    $this->meta()->updateOrCreate(
        [
            'application_id' => $this->id,
            'meta_key' => $key,
        ],
        [
            'meta_value' => $value,
        ]
    );
}

public function getMeta(string $key, mixed $default = null, bool $asArray = true): mixed
{
    $meta = $this->meta()->where('meta_key', $key)->first();
    if (!$meta) {
        return $default;
    }

    $value = $meta->meta_value;

    // Attempt to decode JSON
    $decoded = json_decode($value, $asArray);
    return $decoded !== null ? $decoded : $value;
}
  public function atoApplication()
    {
        return $this->hasOne(AtoApplication::class);
    }
    public function options()
{
    return $this->hasManyThrough(
        ApplicationOption::class,
        UserApplicationSelection::class,
        'application_id', // FK on selections table
        'id',             // FK on application_options table
        'id',             // Local key on application_forms
        'option_id'       // Key on selections linking to options
    );
}
}
