<?php

use App\Models\Currency;
use Database\Factories\AccountFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('show currencies list page', function () {
    CurrencyFactory::times(3)->create();

    actingAs(UserFactory::new()->createOne())
        ->get(route('currencies.index'))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('currencies/List')
                ->has('currencies', 3);
        });
});

test('create currency', function () {
    actingAs(UserFactory::new()->createOne())
        ->post(route('currencies.store'), [
            'code' => 'USD',
            'base' => [10],
            'exponent' => 2
        ])
        ->assertValid()
        ->assertRedirect(route('currencies.index'));

    assertDatabaseHas('currencies', [
        'code' => 'USD',
        // 'base' => "[10]",
        'exponent' => 2
    ]);
});

test('update currency', function () {
    $currency = CurrencyFactory::new()->createOne(['code' => 'USS', 'base' => [9], 'exponent' => 1]);

    actingAs(UserFactory::new()->createOne())
        ->put(route('currencies.update', [$currency]), [
            'code' => 'USD',
            'base' => [10],
            'exponent' => 2
        ])
        ->assertValid()
        ->assertRedirect(route('currencies.index'));

    assertDatabaseHas('currencies', [
        'code' => 'USD',
        // 'base' => "[10]",
        'exponent' => 2
    ]);
});

test('delete currency', function () {
    AccountFactory::times(2)->create();

    $currency = CurrencyFactory::new()->createOne(['code' => 'USD', 'base' => [10], 'exponent' => 2]);

    AccountFactory::times(2)->create(['currency' => $currency->code]);

    assertDatabaseCount('accounts', 4);

    actingAs(UserFactory::new()->createOne())
        ->delete(route('currencies.destroy', [$currency]))
        ->assertRedirect(route('currencies.index'));

    assertDatabaseMissing('currencies', [
        'code' => 'USD',
        // 'base' => "[10]",
        'exponent' => 2
    ]);

    // Assert accounts using the deleted currency where deleted
    assertDatabaseCount('accounts', 2);
    assertDatabaseMissing('accounts', [
        'currency' => 'USD'
    ]);
});
