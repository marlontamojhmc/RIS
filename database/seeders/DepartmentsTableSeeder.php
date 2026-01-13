<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['department_code' => 'BOA', 'department_name' => 'Board of Directors'],
            ['department_code' => 'IAO', 'department_name' => 'Internal Audit Office'],
            ['department_code' => 'OCS', 'department_name' => 'Office of the Corporate Secretary'],
            ['department_code' => 'OPCEO', 'department_name' => 'Office of the President and CEO'],
            ['department_code' => 'BDD', 'department_name' => 'Business Development Department'],
            ['department_code' => 'LG', 'department_name' => 'Legal Department'],
            ['department_code' => 'SSD', 'department_name' => 'Safety and Security Department'],
            ['department_code' => 'OVPCOO', 'department_name' => 'Office of the President and COO'],
            ['department_code' => 'ASD', 'department_name' => 'Administrative Service Department'],
            ['department_code' => 'FSD', 'department_name' => 'Finance Services Department'],
            ['department_code' => 'EAMD', 'department_name' => 'Environment and Asset Management Department'],
            ['department_code' => 'SEZAD', 'department_name' => 'Special Economic Zone Administrative Department'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->insert([
                'department_code' => $dept['department_code'],
                'department_name' => $dept['department_name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
