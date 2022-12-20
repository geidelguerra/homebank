<?php

namespace App\Services;

use App\Support\DateRange;
use App\Support\ReportAggregateFunction;
use App\Support\ReportDimension;
use App\Support\ReportField;
use App\Support\ReportFilter;
use App\Support\ReportFilterOperator;
use App\Support\ReportMetric;
use Carbon\CarbonInterval;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

    public function series(ReportAggregateFunction $aggr): Collection
    {
        $column = match ($this->metric) {
            ReportMetric::Amount => 'transactions.amount'
        };

        $select = match ($aggr) {
            ReportAggregateFunction::Avg => 'AVG('.$column.') as value',
            ReportAggregateFunction::Count => 'COUNT(*) as value',
            ReportAggregateFunction::Max => 'MAX('.$column.') as value',
            ReportAggregateFunction::Min => 'MIN('.$column.') as value',
            ReportAggregateFunction::Sum => 'SUM('.$column.') as value',
        };

        $results = $this->makeQuery()->selectRaw($select . ','. $this->getGroupBy('transactions') .' as date')->get();

        // Fill blank results
        $interval = match ($this->dimension) {
            ReportDimension::Day => CarbonInterval::day(),
            ReportDimension::Month => CarbonInterval::month(),
            ReportDimension::Year => CarbonInterval::year(),
        };

        $format = match ($this->dimension) {
            ReportDimension::Day => 'Y-m-d',
            ReportDimension::Month => 'Y-m',
            ReportDimension::Year => 'Y',
        };

        return collect($this->dateRange->dates($interval, $format))->map(function ($date) use ($results) {
            $item = $results->where('date', $date)->first();

            if (!$item) {
                return null;
            }

            return $item->value;
        });
    }

    private function makeQuery(): Builder
    {
        return DB::table('transactions')
            ->when(count($this->filters) > 0, function (Builder $query) {
                $this->applyFilters($query);
            })
            ->when($this->dateRange, function (Builder $query) {
                $query->whereBetween('transactions.date', [$this->dateRange->startDate, $this->dateRange->endDate]);
            })
            ->groupByRaw($this->getGroupBy('transactions'))
            ->orderByRaw($this->getGroupBy('transactions'));
    }

    // private function makeQuery(): Builder
    // {
    //     $query = DB::table('transactions')
    //         ->when(count($this->filters) > 0, function (Builder $query) {
    //             foreach ($this->filters as $filter) {
    //                 self::applyFilter($query, $filter);
    //             }
    //         });

    //     if ($this->dateRange) {
    //         return with($this->createDatesTable(), function ($tableName) use ($query) {
    //             $groupBy = $this->getGroupBy($tableName);

    //             return $query
    //                 ->rightJoin($tableName, 'transactions.date', '=', $tableName.'.date')
    //                 ->whereBetween($tableName.'.date', [$this->dateRange->startDate, $this->dateRange->endDate])
    //                 ->groupByRaw($groupBy)
    //                 ->orderByRaw($groupBy);
    //         });
    //     }

    //     $groupBy = $this->getGroupBy('transactions');

    //     return $query->groupByRaw($groupBy)->orderByRaw($groupBy);
    // }

    private function getGroupBy(string $tableName): string
    {
        return match ($this->dimension) {
            ReportDimension::Day => "DATE_FORMAT(`{$tableName}`.`date`, '%Y-%m-%d')",
            ReportDimension::Month => "DATE_FORMAT(`{$tableName}`.`date`, '%Y-%m')",
            ReportDimension::Year => "DATE_FORMAT(`{$tableName}`.`date`, '%Y')",
        };
    }

    private function createDatesTable(): string
    {
        $tableName = Str::uuid()->toString();

        Schema::create($tableName, function (Blueprint $table) {
            $table->temporary();
            $table->date('date')->primary();
        });

        $interval = match ($this->dimension) {
            ReportDimension::Day => CarbonInterval::day(),
            ReportDimension::Month => CarbonInterval::month(),
            ReportDimension::Year => CarbonInterval::year(),
        };

        DB::table($tableName)->insert(collect($this->dateRange->dates($interval, 'Y-m-d'))->map(fn ($date) => ['date' => $date])->all());

        return $tableName;
    }

    private function applyFilters(Builder $query): void
    {
        foreach ($this->filters as $filter) {
            self::applyFilter($query, $filter);
        }
    }

    private static function applyFilter(Builder $query, ReportFilter $filter): void
    {
        if ($filter->getField() === ReportField::Amount) {
            $query->where('transactions.amount', self::filterOperatorToSQL($filter->getOperator()), $filter->getValue());

            return;
        }

        if ($filter->getField() === ReportField::Currency) {
            $query->rightJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->where('accounts.currency_code', self::filterOperatorToSQL($filter->getOperator()), $filter->getValue());
        }
    }

    private static function filterOperatorToSQL(ReportFilterOperator $operator): string
    {
        return match ($operator) {
            ReportFilterOperator::Equals => '=',
            ReportFilterOperator::GreaterThan => '>',
            ReportFilterOperator::LesserThan => '<',
        };
    }
}
