<?php

namespace Database\Seeders;

use Database\Factories\AccountFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        CurrencyFactory::new()->createOne(['code' => 'USD', 'base' => [10], 'exponent' => 2]);
        CurrencyFactory::new()->createOne(['code' => 'EUR', 'base' => [10], 'exponent' => 2]);

        AccountFactory::new()->createOne(['name' => 'Cash', 'currency_code' => 'USD']);

        AccountFactory::new()->createOne(['name' => 'Cash', 'currency_code' => 'EUR']);

        AccountFactory::new()->createOne(['name' => 'Credit Card', 'currency_code' => 'USD']);
    }
}
