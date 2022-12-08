<?php

use Database\Factories\AccountFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('show accounts list page', function () {
    get(route('accounts.index'))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('accounts/List');
    });
});

test('show account page', function () {
    $account = AccountFactory::new()->create();

    get(route('accounts.show', [$account]))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('accounts/View');
    });
});

test('show account creation page', function () {
    get(route('accounts.create'))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('accounts/Edit');
    });
});

test('create account', function () {
    assertDatabaseMissing('accounts', [
        'name' => 'My Account',
        'currency' => 'USD'
    ]);

    post(route('accounts.store'), [
        'name' => 'My Account',
        'currency' => 'USD'
    ])->assertRedirect(route('accounts.index'));

    assertDatabaseHas('accounts', [
        'name' => 'My Account',
        'currency' => 'USD'
    ]);
});

test('show update account page', function () {
    $account = AccountFactory::new()->create();

    get(route('accounts.edit', [$account]))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('accounts/Edit');
    });
});

test('update account', function () {
    $account = AccountFactory::new()->create([
        'name' => 'My Account',
        'currency' => 'USD'
    ]);

    put(route('accounts.update', [$account]), [
        'name' => 'My Account 2',
        'currency' => 'EUR'
    ])->assertRedirect(route('accounts.index'));

    assertDatabaseHas('accounts', [
        'name' =>
        'My Account 2',
        'currency' => 'EUR'
    ]);
});


test('delete account', function () {
    $account = AccountFactory::new()->create([
        'name' => 'My Account',
        'currency' => 'USD'
    ]);

    assertDatabaseHas('accounts', [
        'name' => $account->name,
        'currency' => $account->currency
    ]);

    delete(route('accounts.destroy', [$account]))->assertRedirect(route('accounts.index'));

    assertDatabaseMissing('accounts', [
        'name' => $account->name,
        'currency' => $account->currency
    ]);
});
