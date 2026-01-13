<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TfbostaTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tfbosta')->insert([
            [
                'business_type_id' => 3, // Trade Fairs, Bazaars, and other Similar Trade Activities
                'accreditation_type' => 'TFBOSTA-30',
                'description' => 'Duration of 1 to 30 days',
                'price' => 1500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 3,
                'accreditation_type' => 'TFBOSTA-60',
                'description' => 'Duration of 1 to 60 days',
                'price' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 3,
                'accreditation_type' => 'TFBOSTA-90',
                'description' => 'Duration of 1 to 90 days',
                'price' => 3500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 3,
                'accreditation_type' => 'TF-concessionaire',
                'description' => 'TF-per concessionaire',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
