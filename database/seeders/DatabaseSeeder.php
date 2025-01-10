<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a user with a predefined password
        User::create([
            'name' => 'Super Admin',
            'email' => 'adminjsithub@yopmail.com',
            'role' => 'superadmin',
            'password' => Hash::make('Jsithub@123'), // Ensure the password is hashed
        ]);
    }
}
