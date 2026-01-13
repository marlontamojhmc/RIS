<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CeocTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ceoc')->insert([
            [
                'business_type_id' => 2, // assuming CEOC id in business_types table is 2
                'accreditation_type' => 'small-scale',
                'description' => 'Event with a limited footprint of less than 100 attendees',
                'price' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 2,
                'accreditation_type' => 'medium-scale',
                'description' => 'Event with a limited footprint of 101–500 attendees',
                'price' => 1500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 2,
                'accreditation_type' => 'large-scale',
                'description' => 'Event with a limited footprint of more than 500 attendees',
                'price' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 2,
                'accreditation_type' => 'CE-concessionaire',
                'description' => 'CE-per concessionaire',
                'price' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
