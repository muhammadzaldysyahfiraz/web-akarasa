<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $nameColumn = Schema::hasColumn('users', 'nama') ? 'nama' : 'name';

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@akarasa.com'],
            [
                $nameColumn => 'Admin Akarasa',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}