<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

class DateRange implements Arrayable
{
    public function __construct(
        public CarbonImmutable $startDate,
        public CarbonImmutable $endDate
    ) {
        //
    }

    /**
     * @param DateTimeInterface|string|null $now
     */
    public static function fromDateRangePreset(DateRangePreset $preset, $now = null, $timezone = null): static
    {
        $now = Carbon::parse($now, $timezone)->toImmutable();

        return match ($preset) {
            DateRangePreset::CurrentMonth => new static($now->startOfMonth(), $now),
            DateRangePreset::CurrentWeek => new static($now->startOfWeek(), $now),
            DateRangePreset::CurrentYear => new static($now->startOfYear(), $now),
            DateRangePreset::LastMonth => new static($now->subMonth()->startOfMonth(), $now->subMonth()->endOfMonth()),
            DateRangePreset::LastWeek => new static($now->subWeek()->startOfWeek(), $now->subWeek()->endOfWeek()),
            DateRangePreset::LastYear => new static($now->subYear()->startOfYear(), $now->subYear()->endOfYear()),
            DateRangePreset::Today => new static($now->startOfDay(), $now),
            DateRangePreset::Yesterday => new static($now->subDay()->startOfDay(), $now),
        };
    }

    /**
     * @return string[]
     */
    public function dates(CarbonInterval $interval, string $format): array
    {
        return collect(
            CarbonPeriod::between($this->startDate, $this->endDate)
                ->interval($interval)
                ->map(fn ($date) => $date->format($format))
        )
            ->all();
    }

    public function toArray()
    {
        return [
            'startDate' => $this->startDate->toRfc850String(),
            'endDate' => $this->endDate->toRfc850String()
        ];
    }
}
