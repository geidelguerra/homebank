<?php

use App\Models\Transfer;
use Database\Factories\AccountFactory;
use Database\Factories\CategoryFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

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
            'category_id' => CategoryFactory::new()->createOne()->getKey(),
            'source_account_id' => $sourceAccount->getKey(),
            'destination_account_id' => $destinationAccount->getKey()
        ])
        ->assertValid()
        ->assertRedirect(route('home'));

    // Assert
    assertDatabaseCount('transactions', 4);

    assertDatabaseHas('transactions', [
        'amount' => -2000,
        'account_id' => $sourceAccount->getKey()
    ]);

    assertDatabaseHas('transactions', [
        'amount' => 2000,
        'account_id' => $destinationAccount->getKey()
    ]);

    assertDatabaseHas('transfers', [
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
