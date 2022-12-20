<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Currency;
use App\Models\User;
use Database\Factories\AccountFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => env('DEFAULT_USER_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('DEFAULT_USER_PASSWORD', 'password')),
        ]);

        Currency::create(['code' => 'USD', 'base' => [10], 'exponent' => 2]);
        Currency::create(['code' => 'EUR', 'base' => [10], 'exponent' => 2]);
        Currency::create(['code' => 'CUP', 'base' => [10], 'exponent' => 2]);
        Currency::create(['code' => 'MLC', 'base' => [10], 'exponent' => 2]);

        Category::create(['name' => 'Food']);
        Category::create(['name' => 'Electricity']);
        Category::create(['name' => 'Salary']);
        Category::create(['name' => 'Rent']);
    }
}
