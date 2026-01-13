<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locator\Form;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            [
                'name' => 'Gate Clearance',
                'description' => 'Application for gate clearance',
                'approver_group_id' => 1, // adjust this ID as needed
            ],
            [
                'name' => 'Bring In Clearance',
                'description' => 'Application for bringing items inside',
                'approver_group_id' => 1, // adjust this ID as needed
            ],
            [
                'name' => 'Bring Out Clearance',
                'description' => 'Application for bringing items outside',
                'approver_group_id' => 1, // adjust this ID as needed
            ],
            [
                'name' => 'Temporary Bring Out Clearance',
                'description' => 'Temporary bring-out application',
                'approver_group_id' => 1, // adjust this ID as needed
            ],
            [
                'name' => 'Local Purchase',
                'description' => 'Local purchase request',
                'approver_group_id' => 1, // adjust this ID as needed
            ],
        ];

        foreach ($forms as $form) {
            Form::updateOrCreate(
                ['name' => $form['name']], // prevent duplicates
                $form
            );
        }
    }
}
