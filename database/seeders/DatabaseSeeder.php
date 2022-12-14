<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\CurrencyFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => env('DEFAULT_USER_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('DEFAULT_USER_PASSWORD', 'password'))
        ]);

        Currency::create(['code' => 'USD', 'base' => [10], 'exponent' => 2]);
        Currency::create(['code' => 'EUR', 'base' => [10], 'exponent' => 2]);
    }
}
