<?php

use Database\Factories\AccountFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('fail to show accounts list page for guest users', function () {
    get(route('accounts.index'))->assertRedirect(route('login.show'));
});

test('show accounts list page', function () {
    AccountFactory::times(3)->create();

    actingAs(UserFactory::new()->createOne())
        ->get(route('accounts.index'))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('accounts/List')
                ->has('accounts', 3);
        });
});

test('fail to show account page for guest users', function () {
    $account = AccountFactory::new()->createOne();

    get(route('accounts.show', [$account]))->assertRedirect(route('login.show'));
});

test('show account page', function () {
    $account = AccountFactory::new()->createOne();

    actingAs(UserFactory::new()->createOne())
        ->get(route('accounts.show', [$account]))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('accounts/View');
        });
});

test('fail to show account creation page for guest users', function () {
    get(route('accounts.create'))->assertRedirect(route('login.show'));
});

test('show account creation page', function () {
    actingAs(UserFactory::new()->createOne())
        ->get(route('accounts.create'))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('accounts/Edit');
        });
});

test('fail to create account for guest users', function () {
    post(route('accounts.store'), [
        'name' => 'My Account',
        'currency_code' => 'USD'
    ])->assertRedirect(route('login.show'));
});

test('fail to create account if currency_code does not exists', function () {
    actingAs(UserFactory::new()->createOne())
        ->post(route('accounts.store'), [
            'name' => 'My Account',
            'currency_code' => 'USD'
        ])
            ->assertInvalid(['currency_code' => 'The selected currency code is invalid.']);
});

test('create account', function () {
    assertDatabaseMissing('accounts', [
        'name' => 'My Account',
        'currency_code' => 'USD'
    ]);

    actingAs(UserFactory::new()->createOne())
        ->post(route('accounts.store'), [
            'name' => 'My Account',
            'currency_code' => CurrencyFactory::new()->createOne(['code' => 'USD'])->code,
        ])->assertRedirect(route('accounts.index'));

    assertDatabaseHas('accounts', [
        'name' => 'My Account',
        'currency_code' => 'USD'
    ]);
});

test('fail to show update account page for guest users', function () {
    $account = AccountFactory::new()->createOne();

    get(route('accounts.edit', [$account]))->assertRedirect(route('login.show'));
});

test('show update account page', function () {
    $account = AccountFactory::new()->createOne();

    actingAs(UserFactory::new()->createOne())
        ->get(route('accounts.edit', [$account]))
        ->assertInertia(function (AssertableInertia $inertia) {
            $inertia->component('accounts/Edit');
        });
});

test('fail to update account for guest users', function () {
    $account = AccountFactory::new()->createOne([
        'name' => 'My Account',
        'currency_code' => CurrencyFactory::new()->createOne(['code' => 'USD'])->code
    ]);

    put(route('accounts.update', [$account]), [
        'name' => 'My Account 2',
        'currency_code' => CurrencyFactory::new()->createOne(['code' => 'EUR'])->code
    ])->assertRedirect(route('login.show'));
});

test('update account', function () {
    $account = AccountFactory::new()->createOne([
        'name' => 'My Account',
        'currency_code' => CurrencyFactory::new()->createOne(['code' => 'USD'])->code
    ]);

    actingAs(UserFactory::new()->createOne())
        ->put(route('accounts.update', [$account]), [
            'name' => 'My Account 2',
            'currency_code' => CurrencyFactory::new()->createOne(['code' => 'EUR'])->code
        ])->assertRedirect(route('accounts.index'));

    assertDatabaseHas('accounts', [
        'name' =>
        'My Account 2',
        'currency_code' => 'EUR'
    ]);
});

test('fail to delete account for guest users', function () {
    $account = AccountFactory::new()->createOne();
    $account->transactions()->save(TransactionFactory::new()->makeOne());

    delete(route('accounts.destroy', [$account]))->assertRedirect(route('login.show'));
});

test('fail to delete account with transactions', function () {
    $account = AccountFactory::new()->createOne();
    $account->transactions()->saveMany(TransactionFactory::times(3)->income()->make());

    assertDatabaseCount('transactions', 3);

    actingAs(UserFactory::new()->createOne())
        ->delete(route('accounts.destroy', [$account]))
        ->assertRedirect('/')
        ->assertSessionHas('message', 'You can not delete this account because it has transactions. First delete all transactions');

    assertDatabaseCount('transactions', 3);
});

test('delete account', function () {
    $account = AccountFactory::new()->createOne();

    assertDatabaseHas('accounts', [
        'name' => $account->name,
        'currency_code' => $account->currency_code
    ]);

    actingAs(UserFactory::new()->createOne())
        ->delete(route('accounts.destroy', [$account]))
        ->assertRedirect(route('accounts.index'));

    assertDatabaseMissing('accounts', [
        'name' => $account->name,
        'currency_code' => $account->currency_code
    ]);
});
