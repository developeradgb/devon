<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin User
        User::create([
           'name' => 'Admin',
           'email' => 'admin@devon.com',
           'password' => Hash::make('Password@123')
        ]);

        // Normal User
        User::create([
            'name' => 'User',
            'email' => 'user@devon.com',
            'password' => Hash::make('PasswordUser@123')
        ]);
    }
}
