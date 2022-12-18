<?php

namespace App\Services;

use App\Support\DateRange;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function incomeVsExpense(
        DateRange $dateRange,
        string $currencyCode,
        string $groupByDateFormat = '%Y-%m',
    ): array {
        return [
            [
                'label' => 'Income',
                'data' => $this->income($dateRange, $currencyCode, $groupByDateFormat),
                'borderColor' => 'rgba(0, 255, 0)',
                'backgroundColor' => 'rgba(0, 255, 0)',
            ],
            [
                'label' => 'Expense',
                'data' => $this->expense($dateRange, $currencyCode, $groupByDateFormat),
                'borderColor' => 'rgba(255, 0, 0)',
                'backgroundColor' => 'rgba(255, 0, 0)'
            ]
        ];
    }

    private function income(
        DateRange $dateRange,
        string $currencyCode,
        string $groupByDateFormat,
    ): array {
        return DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('transactions.amount', '>', 0)
            ->where('accounts.currency_code', $currencyCode)
            ->whereBetween('transactions.date', [$dateRange->startDate, $dateRange->endDate])
            ->selectRaw("SUM(transactions.amount) as value")
            ->groupByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
            ->orderByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
            ->pluck('value')
            ->all();
    }

    private function expense(
        DateRange $dateRange,
        string $currencyCode,
        string $groupByDateFormat,
    ): array {
        return DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('transactions.amount', '<', 0)
            ->where('accounts.currency_code', $currencyCode)
            ->whereBetween('transactions.date', [$dateRange->startDate, $dateRange->endDate])
            ->selectRaw("SUM(transactions.amount) * -1 as value")
            ->groupByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
            ->orderByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
            ->pluck('value')
            ->all();
    }
}
