<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $businessTypes = [
            [
                'name' => 'SPSNBE',
                'description' => 'Service Provider/Supplier of Non-registered Business Enterprise',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'CEOC',
                'description' => 'Commercial Event Operators and Concessionaries',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'TFBOSTA',
                'description' => 'Trade Fairs, Bazaars, and other Similar Trade Activities',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'VME',
                'description' => 'Vendors and Micro Entrepreneurs',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'PG',
                'description' => 'Provisional Grant',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('business_types')->insert($businessTypes);
    }
}
