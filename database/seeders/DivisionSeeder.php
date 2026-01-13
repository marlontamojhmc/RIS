<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('divisions')->insert([
            [
                'department_id' => 9,
                'division_code' => 'HRSD',
                'division_name' => 'Human Resource Services Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 9,
                'division_code' => 'GSD',
                'division_name' => 'General Services Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 9,
                'division_code' => 'ICTD',
                'division_name' => 'Information and Communications Technology Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 11,
                'division_code' => 'EMD',
                'division_name' => 'Environment Management Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 11,
                'division_code' => 'LAMD',
                'division_name' => 'Land and Asset Management Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 11,
                'division_code' => 'PMD',
                'division_name' => 'Project Management Division',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
