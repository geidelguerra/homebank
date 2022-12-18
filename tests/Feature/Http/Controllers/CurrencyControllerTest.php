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

test('fail to show create currencypage to guest user', function () {
    $currency= CurrencyFactory::new()->createOne();

    get(route('currencies.create', [$currency]))->assertRedirect(route('login.show'));
});

test('show create currency page', function () {
    $currency= CurrencyFactory::new()->createOne();

    actingAs(UserFactory::new()->createOne(), 'web')
        ->get(route('currencies.create', [$currency]))->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('currencies/Edit');
        });
});

test('fail to create currency for guest user', function () {
    post(route('currencies.store'))->assertRedirect(route('login.show'));
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

test('fail to show edit currency page for guest user', function () {
    $currency = CurrencyFactory::new()->createOne();

    get(route('currencies.edit', [$currency]))->assertRedirect(route('login.show'));
});

test('show edit currency page', function () {
    $currency = CurrencyFactory::new()->createOne();

    actingAs(UserFactory::new()->createOne(), 'web')
        ->get(route('currencies.edit', [$currency]))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('currencies/Edit');
        });
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

test('fail to delete currency if there is accounts using it', function () {
    $currency = CurrencyFactory::new()->createOne();

    AccountFactory::times(2)->create(['currency_code' => $currency->code]);

    actingAs(UserFactory::new()->createOne())
        ->delete(route('currencies.destroy', [$currency]))
        ->assertRedirect('/')
        ->assertSessionHas('message', 'You can not delete this currency because there is accounts using it. First delete those accounts or change their currency');

    assertDatabaseCount('accounts', 2);
});

test('delete currency', function () {
    $currency = CurrencyFactory::new()->createOne();

    actingAs(UserFactory::new()->createOne())
        ->delete(route('currencies.destroy', [$currency]))
        ->assertRedirect(route('currencies.index'));

    assertDatabaseMissing('currencies', [
        'code' => 'USD',
        // 'base' => "[10]",
        'exponent' => 2
    ]);
});
