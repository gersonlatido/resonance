<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Create default admin account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'System Admin',
                'email' => 'admin@silogcafe.com',
                'password' => Hash::make('admin123'),
                'position' => 'admin',
            ]
        );

        // Create default cashier account
        User::updateOrCreate(
            ['username' => 'cashier'],
            [
                'name' => 'Main Cashier',
                'email' => 'cashier@silogcafe.com',
                'password' => Hash::make('cashier123'),
                'position' => 'cashier',
            ]
        );

        $this->call([
            InventoryAndRecipesSeeder::class,
            RecipesSeeder::class,
        ]);
    }
}