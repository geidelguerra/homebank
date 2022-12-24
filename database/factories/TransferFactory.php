<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeThisYear(),
            'amount' => $this->faker->numberBetween(100, 1000),
            'exchange_rate' => 1,
            'source_transaction_id' => TransactionFactory::new(),
            'destination_transaction_id' => TransactionFactory::new(),
        ];
    }
}
