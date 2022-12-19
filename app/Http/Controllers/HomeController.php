<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use App\Services\DateLabelsService;
use App\Services\ReportService;
use App\Support\DateRange;
use App\Support\DateRangePreset;
use App\Support\ReportAggregateFunction;
use App\Support\ReportDimension;
use App\Support\ReportField;
use App\Support\ReportFilter;
use App\Support\ReportMetric;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request, ReportService $report)
    {
        $selectedDateRangePreset = DateRangePreset::from($request->string('date_range_preset', DateRangePreset::CurrentYear->value));

        $selectedDateRange = DateRange::fromDateRangePreset($selectedDateRangePreset);

        $selectedCurrency = Currency::query()->where('code', $request->string('filtered_currency', 'USD'))->first();

        $dimension = match ($selectedDateRangePreset) {
            DateRangePreset::CurrentYear,
            DateRangePreset::LastYear => ReportDimension::Month,
            DateRangePreset::CurrentMonth,
            DateRangePreset::LastMonth,
            DateRangePreset::CurrentWeek,
            DateRangePreset::LastWeek,
            DateRangePreset::Today,
            DateRangePreset::Yesterday => ReportDimension::Day,
        };

        $report->setDateRange($selectedDateRange)->setDimension($dimension);

        return inertia('Home', [
            'incomeVsExpense' => function () use ($report, $selectedDateRangePreset, $selectedDateRange, $selectedCurrency, $groupByDateFormat) {
                return [
                    'labels' => (new DateLabelsService())->fromPreset($selectedDateRangePreset, $selectedDateRange),
                    // 'datasets' => $report->incomeVsExpense($selectedDateRange, $selectedCurrency->getKey(), $groupByDateFormat)
                    'datasets' => [
                        // Income
                        $report->setMetric(ReportMetric::Amount)->setFilters([
                            ReportFilter::field(ReportField::Amount)->greaterThan(0),
                        ])->series(ReportAggregateFunction::Sum),
                        // Expenses
                        $report->setMetric(ReportMetric::Amount)->setFilters([
                            ReportFilter::field(ReportField::Amount)->lesserThan(0),
                        ])->series(ReportAggregateFunction::Sum)
                    ]
                ];
            },
            'availableCurrencies' => function () {
                return Currency::query()->orderBy('code')->get();
            },
            'availableAccounts' => function () {
                return Account::query()->orderBy('name')->get();
            },
            'selectedDateRange' => $selectedDateRange,
            'availableDateRangePresets' => collect(DateRangePreset::cases())->filter(fn ($preset) => !in_array($preset, [DateRangePreset::Today, DateRangePreset::Yesterday]))->values(),
            'selectedDateRangePreset' => $selectedDateRangePreset,
            'selectedCurrency' => $selectedCurrency
        ]);
    }
}
