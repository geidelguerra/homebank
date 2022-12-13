<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        UserFactory::new()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
        ]);
    }
}
