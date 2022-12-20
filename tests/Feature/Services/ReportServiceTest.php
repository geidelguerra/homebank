<?php

use App\Services\ReportService;
use App\Support\ReportDimension;
use App\Support\DateRange;
use App\Support\DateRangePreset;
use App\Support\ReportAggregateFunction;
use App\Support\ReportField;
use App\Support\ReportFilter;
use App\Support\ReportFilterOperator;
use App\Support\ReportMetric;
use Database\Factories\AccountFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Support\Carbon;

test('returns correct time series results for aggregate function', function (ReportAggregateFunction $aggr) {
    // Arrange
    TransactionFactory::new()->create(['date' => '2022-12-01', 'amount' => 1000]);
    TransactionFactory::new()->create(['date' => '2022-12-07', 'amount' => 2000]);
    TransactionFactory::new()->create(['date' => '2022-12-14', 'amount' => 3000]);
    TransactionFactory::new()->create(['date' => '2022-12-14', 'amount' => 4000]);

    // Act
    $data = app(ReportService::class)
        ->setDateRange(new DateRange(Carbon::create(2022, 12, 1)->toImmutable(), Carbon::create(2022, 12, 31)->toImmutable()))
        ->setDimension(ReportDimension::Day)
        ->setMetric(ReportMetric::Amount)
        ->series($aggr)
        ->all();

    // Assert
    expect($data)->toBeArray();

    switch ($aggr) {
        case ReportAggregateFunction::Avg:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                3500,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
        case ReportAggregateFunction::Count:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1,
                null,
                null,
                null,
                null,
                null,
                1,
                null,
                null,
                null,
                null,
                null,
                null,
                2,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
        case ReportAggregateFunction::Max:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                4000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
        case ReportAggregateFunction::Min:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                3000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
        case ReportAggregateFunction::Sum:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                7000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
    }
})->with(
    collect(ReportAggregateFunction::cases())
        ->mapWithKeys(fn (ReportAggregateFunction $case) => [$case->name => $case])
        ->all()
);

test('returns correct time series results for amount filter', function (ReportFilter $filter) {
    // Arrange
    TransactionFactory::new()->create(['date' => '2022-12-01', 'amount' => 1000]);
    TransactionFactory::new()->create(['date' => '2022-12-07', 'amount' => -2000]);
    TransactionFactory::new()->create(['date' => '2022-12-14', 'amount' => 1000]);
    TransactionFactory::new()->create(['date' => '2022-12-14', 'amount' => 1000]);

    // Act
    $data = app(ReportService::class)
        ->setDateRange(new DateRange(Carbon::create(2022, 12, 1)->toImmutable(), Carbon::create(2022, 12, 31)->toImmutable()))
        ->setDimension(ReportDimension::Day)
        ->setMetric(ReportMetric::Amount)
        ->addFilter($filter)
        ->series(ReportAggregateFunction::Sum)
        ->all();

    // Assert
    expect($data)->toBeArray();

    switch ($filter->getOperator()) {
        case ReportFilterOperator::Equals:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;

        case ReportFilterOperator::GreaterThan:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                1000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                2000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;

        case ReportFilterOperator::LesserThan:
            expect($data)->toHaveCount(31);
            expect($data)->toMatchArray([
                null,
                null,
                null,
                null,
                null,
                null,
                -2000,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ]);
            break;
    }
})->with(
    collect([
        ReportFilter::field(ReportField::Amount)->equals(1000),
        ReportFilter::field(ReportField::Amount)->greaterThan(0),
        ReportFilter::field(ReportField::Amount)->lesserThan(0),
    ])->mapWithKeys(fn (ReportFilter $filter) => [$filter->__toString() => $filter])->all()
);

test('returns correct time series results for currency filter', function () {
    // Arrange
    $account1 = AccountFactory::new()->for(CurrencyFactory::new()->createOne(['code' => 'USD']))->createOne();
    TransactionFactory::new()->for($account1)->create(['date' => '2022-12-01', 'amount' => 1000]);
    TransactionFactory::new()->for($account1)->create(['date' => '2022-12-07', 'amount' => -1000]);
    TransactionFactory::new()->for($account1)->create(['date' => '2022-12-14', 'amount' => 1000]);
    TransactionFactory::new()->for($account1)->create(['date' => '2022-12-14', 'amount' => 3000]);

    $account2 = AccountFactory::new()->for(CurrencyFactory::new()->createOne(['code' => "EUR"]))->createOne();
    TransactionFactory::new()->for($account2)->create(['date' => '2022-12-01', 'amount' => 5000]);
    TransactionFactory::new()->for($account2)->create(['date' => '2022-12-07', 'amount' => -1000]);

    // Act
    $data = app(ReportService::class)
        ->setDateRange(new DateRange(Carbon::create(2022, 12, 1)->toImmutable(), Carbon::create(2022, 12, 31)->toImmutable()))
        ->setDimension(ReportDimension::Day)
        ->setMetric(ReportMetric::Amount)
        ->addFilter(ReportFilter::field(ReportField::Currency)->equals('USD'))
        ->series(ReportAggregateFunction::Sum)
        ->all();

    // Assert
    expect($data)->toBeArray();
    expect($data)->toHaveCount(31);
    expect($data)->toMatchArray([
        1000,
        null,
        null,
        null,
        null,
        null,
        -1000,
        null,
        null,
        null,
        null,
        null,
        null,
        4000,
        null,
        null,
        null,
        null,
        null,
        null,
        null,
    ]);
});
