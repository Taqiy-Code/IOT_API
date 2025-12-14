<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Hapus semua data tanpa truncate (aman)
        DB::table('roles')->delete();

        // Reset auto increment
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');

        // Insert data role baru
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'user'],
        ]);
    }
}
