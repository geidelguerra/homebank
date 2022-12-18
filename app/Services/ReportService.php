<?php

namespace App\Services;

use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function incomeVsExpense(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $currency
    ): array {
        return [
            'labels' => $this->labels($startDate, $endDate, CarbonInterval::month(), 'M'),
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $this->income($startDate, $endDate, $currency),
                    'fill' =>  false,
                    'borderColor' => 'green',
                ],
                [
                    'label' => 'Expense',
                    'data' => $this->expense($startDate, $endDate, $currency),
                    'fill' =>  false,
                    'borderColor' => 'red',
                ]
            ]
        ];
    }

    private function income(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $currency
    ): array {
        return DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('transactions.amount', '>', 0)
            ->where('accounts.currency_code', $currency)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->selectRaw("SUM(transactions.amount) as value")
            ->groupByRaw("DATE_FORMAT(transactions.date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(transactions.date, '%Y-%m')")
            ->pluck('value')
            ->all();
    }

    private function expense(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $currency
    ): array {
        return DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->where('transactions.amount', '<', 0)
            ->where('accounts.currency_code', $currency)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->selectRaw("SUM(transactions.amount) * -1 as value")
            ->groupByRaw("DATE_FORMAT(transactions.date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(transactions.date, '%Y-%m')")
            ->pluck('value')
            ->all();
    }

    private function labels(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        CarbonInterval $interval,
        string $format
    ): array {
        return collect(
            CarbonPeriod::between($startDate, $endDate)
                ->interval($interval)
                ->map(fn ($date) => $date->format($format))
        )
            ->all();
    }
}
