<?php

use Database\Factories\AccountFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\TransactionFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('show transactions page', function () {
    get(route('transactions.index'))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('transactions/List');
    });
});

test('show transaction page', function () {
    $transaction = TransactionFactory::new()->createOne();

    get(route('transactions.show', [$transaction]))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('transactions/View');
    });
});

test('show create transaction page', function () {
    $transaction = TransactionFactory::new()->createOne();

    get(route('transactions.create', [$transaction]))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('transactions/Edit');
    });
});

test('create transaction', function () {
    $category = CategoryFactory::new()->createOne(['name' => 'Comida']);
    $account = AccountFactory::new()->createOne(['name' => 'Efectivo', 'currency' => 'CUP']);
    $account->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 18000]));
    $account->updateAmount()->save();

    expect($account->amount)->toBe(18000);
    expect($account->transactions()->count())->toBe(1);

    assertDatabaseMissing('transactions', [
        'date' => '2022-12-09',
        'amount' => -18000,
        'description' => 'Un carton de huevos',
        'category_id' => $category->id,
        'account_id' => $account->id
    ]);

    post(route('transactions.store'), [
        'date' => '2022-12-09',
        'amount' => -18000,
        'description' => 'Un carton de huevos',
        'category_id' => $category->id,
        'account_id' => $account->id
    ])->assertRedirect(route('transactions.index'));

    assertDatabaseHas('transactions', [
        'date' => '2022-12-09',
        'amount' => -18000,
        'description' => 'Un carton de huevos',
        'category_id' => $category->id,
        'account_id' => $account->id
    ]);

    $account->refresh();

    expect($account->amount)->toBe(0);
});

test('show edit transaction page', function () {
    $transaction = TransactionFactory::new()->createOne();

    get(route('transactions.edit', [$transaction]))->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('transactions/Edit');
    });
});

test('update transaction', function () {
    $account = AccountFactory::new()->createOne(['name' => 'Efectivo', 'currency' => 'CUP']);
    $account->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 3600]));

    $transaction = TransactionFactory::new()->createOne([
        'date' => '2022-12-02',
        'description' => 'Patanos',
        'amount' => -1700,
        'account_id' => $account->id,
    ]);

    $account->updateAmount()->save();

    assertDatabaseHas('transactions', [
        'date' => '2022-12-02',
        'description' => 'Patanos',
        'amount' => -1700,
    ]);

    put(route('transactions.update', [$transaction]), [
        'date' => '2022-12-01',
        'description' => 'Platanos',
        'amount' => -1800
    ])->assertRedirect(route('transactions.index'));

    assertDatabaseHas('transactions', [
        'date' => '2022-12-01',
        'description' => 'Platanos',
        'amount' => -1800,
    ]);

    $account->refresh();

    expect($account->amount)->toBe(1800);
});

test('delete transaction', function () {
    $account = AccountFactory::new()->createOne(['name' => 'Efectivo', 'currency' => 'CUP']);

    $transaction = TransactionFactory::new()->createOne(['amount' => 3600, 'account_id' => $account->id]);

    $account->updateAmount()->save();

    expect($account->amount)->toBe(3600);

    delete(route('transactions.destroy', [$transaction]))->assertRedirect(route('transactions.index'));

    assertDatabaseMissing('transactions', [
        'id' => $transaction->id
    ]);

    $account->refresh();

    expect($account->amount)->toBe(0);
});
