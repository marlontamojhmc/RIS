<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Approver_Group_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('approver_groups')->insert([
            'id' => 1,
            'name' => 'Default Approval Group',
            'description' => 'Default group for testing and forms seeding',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}