<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'Admin Utama',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role_id'  => 1, // admin
        ]);

        User::create([
            'name'     => 'User Biasa',
            'email'    => 'user@gmail.com',
            'password' => Hash::make('password123'),
            'role_id'  => 2, // user
        ]);
    }
}
