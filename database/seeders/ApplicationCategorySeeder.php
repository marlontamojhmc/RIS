<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicationCategory;
use App\Models\ApplicationValidity;
use App\Models\ApplicationCategoryOption;

class ApplicationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'P10,000 and below',
            'P10,001 to P50,000',
            'More Than P50,000',
            'Medium construction vehicles',
            'Heavy construction vehicles',
        ];

        $validities = [
            '1 Time validity',
            '1 Day validity',
            '5 Time validity',
            '5 Day validity',
            '20 Time validity',
            '20 Day validity',
        ];

        // Insert categories
        $categoryIds = [];
        foreach ($categories as $name) {
            $categoryIds[] = ApplicationCategory::create(['name' => $name])->id;
        }

        // Insert validities
        $validityIds = [];
        foreach ($validities as $label) {
            $validityIds[] = ApplicationValidity::create(['label' => $label])->id;
        }

        // Example linking (you can extend this to match your original table)
        ApplicationCategoryOption::create([
            'category_id' => $categoryIds[0],
            'validity_id' => $validityIds[0],
            'value' => 1,
        ]);

        ApplicationCategoryOption::create([
            'category_id' => $categoryIds[3],
            'validity_id' => $validityIds[1],
            'value' => 400,
        ]);
    }
}
