<?php

use App\Models\Transfer;
use Database\Factories\AccountFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\TransferFactory;
use Database\Factories\UserFactory;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('show list of transfers', function () {
    // Arrange
    TransferFactory::times(3)->create();

    // Act
    $response = actingAs(UserFactory::new()->createOne())->get(route('transfers.index'));

    // Assert
    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('transfers/List')
            ->has('transfers.data', 3);
    });
});

test('create a transfer', function () {
    // Arrange
    $sourceAccount = AccountFactory::new()
        ->for(CurrencyFactory::new()->createOne(['code' => 'MLC']))
        ->createOne();

    TransactionFactory::new()->for($sourceAccount)->createOne(['amount' => 4000]);

    $sourceAccount->updateAmount()->save();

    $destinationAccount = AccountFactory::new()
        ->for(CurrencyFactory::new()->createOne(['code' => 'USD']))
        ->createOne();

    TransactionFactory::new()->for($destinationAccount)->createOne(['amount' => 6000]);

    $destinationAccount->updateAmount()->save();

    // Act
    actingAs(UserFactory::new()->createOne())
        ->post(route('transfers.store'), [
            'date' => '2022-12-20',
            'amount' => 2000,
            'exchange_rate' => 1,
            'source_account_id' => $sourceAccount->getKey(),
            'destination_account_id' => $destinationAccount->getKey()
        ])
        ->assertValid()
        ->assertRedirect(route('home'));

    // Assert
    assertDatabaseCount('transactions', 4);

    assertDatabaseHas('transactions', [
        'amount' => -2000,
        'date' => '2022-12-20',
        'account_id' => $sourceAccount->getKey()
    ]);

    assertDatabaseHas('transactions', [
        'amount' => 2000,
        'date' => '2022-12-20',
        'account_id' => $destinationAccount->getKey()
    ]);

    assertDatabaseHas('transfers', [
        'date' => '2022-12-20',
        'amount' => 2000,
        'exchange_rate' => 1
    ]);

    with(Transfer::query()->first(), function ($transfer) {
        expect($transfer->sourceTransaction->amount)->toBe(-2000);
        expect($transfer->destinationTransaction->amount)->toBe(2000);
    });

    $sourceAccount->updateAmount()->save();

    expect($sourceAccount->amount)->toBe(2000);

    $destinationAccount->updateAmount()->save();

    expect($destinationAccount->amount)->toBe(8000);
});

test('update a transfer', function () {
    // Arrange
    $transfer = TransferFactory::new()->createOne([
        'amount' => 1000,
        'date'=> '2022-12-11',
        'exchange_rate' => 1
    ]);

    $sourceAccount = AccountFactory::new()->createOne();
    $sourceAccount->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 2000]));
    $sourceAccount->save();

    $transfer->sourceTransaction()->associate(TransactionFactory::new()->for($sourceAccount)->createOne([
        'amount' => -1000,
        'date'=> '2022-12-11',
    ]));

    $destinationAccount = AccountFactory::new()->createOne();
    $destinationAccount->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 2000]));
    $destinationAccount->save();

    $transfer->destinationTransaction()->associate(TransactionFactory::new()->for($destinationAccount)->createOne([
        'amount' => 1000,
        'date'=> '2022-12-11',
    ]));

    $sourceAccount->updateAmount()->save();
    $destinationAccount->updateAmount()->save();

    expect($sourceAccount->amount)->toBe(1000);
    expect($destinationAccount->amount)->toBe(3000);

    $transfer->save();

    // Act
    $response = actingAs(UserFactory::new()->create())
        ->put(route('transfers.update', [$transfer->getKey()]), [
            'amount' => 1001,
            'date'=> '2022-12-12',
            'exchange_rate' => 1.1
        ]);

    // Assert
    $response->assertRedirect(route('transfers.index'));

    $transfer->refresh();
    $sourceAccount->refresh();
    $destinationAccount->refresh();

    expect($transfer->date)->toBe('2022-12-12');
    expect($transfer->amount)->toBe(1001);
    expect($transfer->exchange_rate)->toBe(1.1);
    expect($transfer->sourceTransaction->amount)->toBe(-1001);
    expect($transfer->destinationTransaction->amount)->toBe(1101);
    expect($sourceAccount->amount)->toBe(999);
    expect($destinationAccount->amount)->toBe(3101);
});

test('delete transfer', function () {
    // Arrange
    $sourceAccount = AccountFactory::new()->createOne();
    $sourceAccount->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 2000]));
    $sourceAccount->save();

    $destinationAccount = AccountFactory::new()->createOne();
    $destinationAccount->transactions()->save(TransactionFactory::new()->makeOne(['amount' => 2000]));
    $destinationAccount->save();

    $transfer = TransferFactory::new()->createOne([
        'source_transaction_id' => TransactionFactory::new()->for($sourceAccount)->createOne([
            'amount' => -1000,
            'date'=> '2022-12-11',
        ]),
        'destination_transaction_id' => TransactionFactory::new()->for($destinationAccount)->createOne([
            'amount' => 1000,
            'date'=> '2022-12-11',
        ])
    ]);

    $sourceAccount->updateAmount()->save();
    $destinationAccount->updateAmount()->save();

    assertDatabaseCount('transactions', 4);

    expect($sourceAccount->amount)->toBe(1000);
    expect($destinationAccount->amount)->toBe(3000);

    // Act
    $response = actingAs(UserFactory::new()->createOne())
        ->delete(route('transfers.destroy', [$transfer->getKey()]));

    // Assert
    $response->assertRedirect(route('transfers.index'));

    $sourceAccount->refresh();
    $destinationAccount->refresh();

    assertDatabaseMissing('transfers', [
        'id' => $transfer->getKey()
    ]);

    assertDatabaseMissing('transactions', [
        'id' => $transfer->source_transaction_id
    ]);

    assertDatabaseMissing('transactions', [
        'id' => $transfer->destination_transaction_id
    ]);

    expect($sourceAccount->amount)->toBe(2000);
    expect($destinationAccount->amount)->toBe(2000);
});
