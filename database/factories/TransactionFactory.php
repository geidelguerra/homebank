<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->unique()->dateTimeThisYear(),
            'amount' => $this->faker->numberBetween(-10000, 10000),
            'description' => $this->faker->text(),
            'category_id' => CategoryFactory::new(),
            'account_id' => AccountFactory::new()
        ];
    }

    public function income(int $min = 100, int $max = 100000): static
    {
        return $this->state(function (array $attributes) use ($min, $max) {
            return [
                'amount' => $this->faker->numberBetween($min, $max)
            ];
        });
    }

    public function expense(int $min = -100, int $max = -100000): static
    {
        return $this->state(function (array $attributes) use ($min, $max) {
            return [
                'amount' => $this->faker->numberBetween($min, $max)
            ];
        });
    }
}
