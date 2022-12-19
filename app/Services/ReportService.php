<?php

namespace App\Services;

use App\Support\ReportDimension;
use App\Support\DateRange;
use App\Support\ReportAggregateFunction;
use App\Support\ReportField;
use App\Support\ReportFilter;
use App\Support\ReportFilterOperator;
use App\Support\ReportMetric;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportService
{
    private ?DateRange $dateRange = null;

    private ?ReportDimension $dimension = null;

    private ?ReportMetric $metric = null;

    /**
     * @var ReportFilter[]
     */
    private array $filters = [];

    public function setDateRange(DateRange $dateRange): static
    {
        $this->dateRange = $dateRange;

        return $this;
    }

    public function setDimension(ReportDimension $dimension): static
    {
        $this->dimension = $dimension;

        return $this;
    }

    public function setMetric(ReportMetric $metric): static
    {
        $this->metric = $metric;

        return $this;
    }

    public function addFilter(ReportFilter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function setFilters(array $filters): static
    {
        $this->filters = [...$filters];

        return $this;
    }

    public function series(ReportAggregateFunction $aggr): array
    {
        $column = match ($this->metric) {
            ReportMetric::Amount => 'amount'
        };

        $select = match ($aggr) {
            ReportAggregateFunction::Avg => 'AVG(' . $column . ') as value',
            ReportAggregateFunction::Count => 'COUNT(*) as value',
            ReportAggregateFunction::Max => 'MAX(' . $column . ') as value',
            ReportAggregateFunction::Min => 'MIN(' . $column . ') as value',
            ReportAggregateFunction::Sum => 'SUM(' . $column . ') as value',
        };

        return $this->makeQuery()
            ->selectRaw($select)
            ->pluck('value')
            ->all();
    }

    private function makeQuery(): Builder
    {
        $groupBy = match ($this->dimension) {
            ReportDimension::Day => "DATE_FORMAT(date, '%Y-%m-%d')",
            ReportDimension::Month => "DATE_FORMAT(date, '%Y-%m')",
            ReportDimension::Year => "DATE_FORMAT(date, '%Y')",
        };

        return DB::table('transactions')
            ->where(function (Builder $query) {
                foreach ($this->filters as $filter) {
                    $column = match ($filter->getField()) {
                        ReportField::Amount => 'amount'
                    };

                    $operator = match ($filter->getOperator()) {
                        ReportFilterOperator::Equals => '=',
                        ReportFilterOperator::GreaterThan => '>',
                        ReportFilterOperator::LesserThan => '<',
                    };

                    $query->where($column, $operator, $filter->getValue());
                }
            })
            ->groupByRaw($groupBy)
            ->orderByRaw($groupBy);
    }


    // public function incomeVsExpense(): array
    // {
    //     return [
    //         [
    //             'label' => 'Income',
    //             'data' => $this->income($this->dateRange, $this->currencyCode, $groupByDateFormat),
    //             'borderColor' => 'rgba(0, 255, 0)',
    //             'backgroundColor' => 'rgba(0, 255, 0)',
    //         ],
    //         [
    //             'label' => 'Expense',
    //             'data' => $this->expense($this->dateRange, $this->currencyCode, $groupByDateFormat),
    //             'borderColor' => 'rgba(255, 0, 0)',
    //             'backgroundColor' => 'rgba(255, 0, 0)'
    //         ]
    //     ];
    // }

    // private function income(
    //     DateRange $dateRange,
    //     string $currencyCode,
    //     string $groupByDateFormat,
    // ): array {
    //     return DB::table('transactions')
    //         ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
    //         ->where('transactions.amount', '>', 0)
    //         ->where('accounts.currency_code', $currencyCode)
    //         ->whereBetween('transactions.date', [$dateRange->startDate, $dateRange->endDate])
    //         ->selectRaw("SUM(transactions.amount) as value")
    //         ->groupByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
    //         ->orderByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
    //         ->pluck('value')
    //         ->all();
    // }

    // private function expense(
    //     DateRange $dateRange,
    //     string $currencyCode,
    //     string $groupByDateFormat,
    // ): array {
    //     return DB::table('transactions')
    //         ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
    //         ->where('transactions.amount', '<', 0)
    //         ->where('accounts.currency_code', $currencyCode)
    //         ->whereBetween('transactions.date', [$dateRange->startDate, $dateRange->endDate])
    //         ->selectRaw("SUM(transactions.amount) * -1 as value")
    //         ->groupByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
    //         ->orderByRaw("DATE_FORMAT(transactions.date, '{$groupByDateFormat}')")
    //         ->pluck('value')
    //         ->all();
    // }
}
