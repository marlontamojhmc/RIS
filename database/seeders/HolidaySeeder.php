<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locator\Holiday;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => '2025-01-01',
                'is_recurring' => true,
                'type' => 'regular',
            ],
            [
                'name' => 'Araw ng Kagitingan',
                'date' => '2025-04-09',
                'is_recurring' => true,
                'type' => 'regular',
            ],
            [
                'name' => 'Labor Day',
                'date' => '2025-05-01',
                'is_recurring' => true,
                'type' => 'regular',
            ],
            [
                'name' => 'Independence Day',
                'date' => '2025-06-12',
                'is_recurring' => true,
                'type' => 'regular',
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2025-12-25',
                'is_recurring' => true,
                'type' => 'regular',
            ],
            [
                'name' => 'Rizal Day',
                'date' => '2025-12-30',
                'is_recurring' => true,
                'type' => 'regular',
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }
    }
}
