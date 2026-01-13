<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SpsnbeTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Find the business type ID for SPSNBE
        $businessTypeId = DB::table('business_types')
            ->where('name', 'SPSNBE')
            ->value('id');

        if (!$businessTypeId) {
            throw new \Exception('Business type "SPSNBE" not found. Please seed business_types first.');
        }

        $records = [
            [
                'business_type_id' => $businessTypeId,
                'accreditation_type' => 'Accreditation',
                'price' => 1000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'business_type_id' => $businessTypeId,
                'accreditation_type' => 'Reaccreditation',
                'price' => 500.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('spsnbe')->insert($records);
    }
}
