<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'adminjsithub@yopmail.com',
            'role' => 'superadmin',
            'password' => Hash::make('Jsithub@123'), 
        ]);
    }
}
