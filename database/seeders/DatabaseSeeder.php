<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartmentsTableSeeder::class);
        $this->call(UserFunctionSeeder::class);
        $this->call(Approver_Group_Seeder::class);
        $this->call(FormSeeder::class);
        $this->call(ApplicationOptionsSeeder::class);
        $this->call(HolidaySeeder::class);
        
    }
}
