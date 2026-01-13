<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Create-Read-Update-Delete'],
            ['name' => 'Create-Read-Update'],
            ['name' => 'Create-Read'],
            ['name' => 'Create-Update'],
            ['name' => 'Read'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insert([
                'name' => $perm['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
