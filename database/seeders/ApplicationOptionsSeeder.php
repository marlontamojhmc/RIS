<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locator\ApplicationOption;

class ApplicationOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['price'=>'20','name' => 'P10,000 and below',       'value' => '1',   'validity' => '1 Time validity'],
            ['price'=>'25','name' => 'P10,001 to P50,000',      'value' => '1',   'validity' => '1 Time validity'],
            ['price'=>'30','name' => 'More Than P50,000',       'value' => '1',   'validity' => '1 Time validity'],
            ['price'=>'35','name' => 'Medium construction vehicles', 'value' => '1', 'validity' => '1 Day validity'],
            ['price'=>'40','name' => 'Heavy construction vehicles',  'value' => '1',  'validity' => '1 Day validity'],

            ['price'=>'45','name' => 'P10,000 and below',       'value' => '5',   'validity' => '5 Time validity'],
            ['price'=>'50','name' => 'P10,001 to P50,000',      'value' => '5',   'validity' => '5 Time validity'],
            ['price'=>'55','name' => 'More Than P50,000',       'value' => '5',   'validity' => '5 Time validity'],
            ['price'=>'60','name' => 'Medium construction vehicles', 'value' => '5', 'validity' => '5 Day validity'],
            ['price'=>'65','name' => 'Heavy construction vehicles',  'value' => '5',  'validity' => '5 Day validity'],

           ['price'=>'70','name' => 'P10,000 and below',       'value' => '20',   'validity' => '20 Time validity'],
            ['price'=>'75','name' => 'P10,001 to P50,000',      'value' => '20',   'validity' => '20 Time validity'],
            ['price'=>'80','name' => 'More Than P50,000',       'value' => '20',   'validity' => '20 Time validity'],
            ['price'=>'85','name' => 'Medium construction vehicles', 'value' => '20', 'validity' => '20 Day validity'],
            ['price'=>'90','name' => 'Heavy construction vehicles',  'value' => '20',  'validity' => '20 Day validity'],
        ];

        foreach ($options as $option) {
            ApplicationOption::create($option);
        }
    }
}
