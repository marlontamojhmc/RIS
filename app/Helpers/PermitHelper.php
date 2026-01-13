<?php
namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Locator\Holiday;
use App\Models\Locator\ApplicationModel;

class PermitHelper
{
    public static function computeValidity(int $days)
    {
        $date = Carbon::now();
        $added = 0;

        // Get all holiday dates (format to Y-m-d for easy comparison)
        $holidays = Holiday::pluck('date')->map(function ($d) {
            return Carbon::parse($d)->format('Y-m-d');
        })->toArray();

        while ($added < $days) {
            $date->addDay();

            // Skip weekends
            if (in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                continue;
            }

            // Skip holidays
            if (in_array($date->format('Y-m-d'), $holidays)) {
                continue;
            }

            $added++;
        }

        return $date;
    }
   
   public static function controlNumberGenerate()
{
    // Get current year and NON-zero-padded month
    $year = date('Y');
    $month = date('n'); // <-- this is 1–12 without leading zero

    // Find last generated control number for THIS year & month
    $last = ApplicationModel::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderBy('id', 'desc')
        ->first();

    // Default sequence = 1
    $sequence = 1;

    if ($last && !empty($last->control_number)) {
        $parts = explode('-', $last->control_number);
        $sequence = intval($parts[2]) + 1; // increment last sequence
    }

    // Format as 4 digits: 0001
    $formattedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

    // Final control number (yyyy-month-0001)
    return "{$year}-{$month}-{$formattedSequence}";
}

}
