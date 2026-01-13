<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VmeTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vme')->insert([
            [
                'business_type_id' => 4, // Vendors and Micro Entrepreneurs
                'accreditation_type' => 'Accreditation',
                'price' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 4,
                'accreditation_type' => 'Reaccreditation',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
