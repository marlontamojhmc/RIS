<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvisionalGrantTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('provisional_grant')->insert([
            [
                'business_type_id' => 5,
                'pg_type' => 'business_enterprise',
                'description' => 'Business Enterprise',
                'price' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'regular_SPS',
                'description' => 'Regular Service Providers and Suppliers',
                'price' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'vendors',
                'description' => 'Vendors (i.e. with no or minimal set-up, pushcarts, trays)',
                'price' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'micro-entrepreneurs',
                'description' => 'Micro-entrepreneurs',
                'price' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'resident-1-5',
                'description' => 'Resident accommodation providers offering 1–5 unit/s for lodging',
                'price' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'resident-6-10',
                'description' => 'Resident accommodation providers offering 6–10 units for lodging',
                'price' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_type_id' => 5,
                'pg_type' => 'resident-10-more',
                'description' => 'Resident accommodation providers offering more than 10 units for lodging',
                'price' => 350,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
