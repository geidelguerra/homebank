<?php

namespace App\Services;

use App\Support\DateRange;
use App\Support\DateRangePreset;
use Carbon\CarbonInterval;

class DateLabelsService
{
    /**
     * @return string[]
     */
    public function fromPreset(DateRangePreset $preset, DateRange $dateRange): array
    {
        return match ($preset) {
            DateRangePreset::CurrentMonth,
            DateRangePreset::LastMonth,
            DateRangePreset::CurrentWeek,
            DateRangePreset::LastWeek => $dateRange->dates(CarbonInterval::day(), 'd'),
            DateRangePreset::CurrentYear,
            DateRangePreset::LastYear => $dateRange->dates(CarbonInterval::month(), 'M'),
        };
    }
}
