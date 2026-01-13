<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFunctionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_function')->insert([
            ['department_id' => 12, 'function' => 'Accreditation Center', 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 12, 'function' => 'Customs Clearance Center', 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 12, 'function' => 'Labor Center', 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 12, 'function' => 'One Stop Action Center', 'created_at' => now(), 'updated_at' => now()],        
            ['department_id' => 12, 'function' => 'SEZAD Manager', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
