<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Safe Insert (No duplicate error)
        User::updateOrCreate(
            ['email' => 'adminjsithub@yopmail.com'],
            [
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'password' => Hash::make('Jsithub@123'),
            ]
        );

        // Call Country Seeder
        $this->call(CountrySeeder::class);
    }
}
