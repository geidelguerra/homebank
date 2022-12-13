<?php

namespace Database\Seeders;

use Database\Factories\AccountFactory;
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

        AccountFactory::new()->createOne(['name' => 'Cash', 'currency' => 'USD']);

        AccountFactory::new()->createOne(['name' => 'Cash', 'currency' => 'EUR']);

        AccountFactory::new()->createOne(['name' => 'Credit Card', 'currency' => 'USD']);
    }
}
