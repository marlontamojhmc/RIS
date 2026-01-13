<?php

namespace App\Models\Signup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Signup\CEOC;
use App\Models\Signup\ProvitionalGrant;
use App\Models\Signup\SPSNBE;
use App\Models\Signup\TFBOSTA;
use App\Models\Signup\VME;

class BusinessType extends Model
{
    use HasFactory;
    protected $table = 'business_types'; 

    protected $fillable = ['id', 'name', 'description'];

    // Relationships
    public function ceoc()
    {
        return $this->hasMany(CEOC::class);
    }

    public function provitionalGrant()
    {
        return $this->hasMany(ProvitionalGrant::class);
    }

    public function spsnbe()
    {
        return $this->hasMany(SPSNBE::class);
    }

    public function tfbosta()
    {
        return $this->hasMany(TFBOSTA::class);
    }

    public function vme()
    {
        return $this->hasMany(VME::class);
    }

    // Optional unified method for "categories" if you want to treat any type as a category dropdown
    public function categories()
    {
        // Example: returning CEOC as default category type
        return $this->ceoc();
    }
}
