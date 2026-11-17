<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'nama' => 'Admin Akarasa',
            'password' => 'admin123',
            'role' => 'admin',
        ];

        if (Schema::hasColumn('users', 'nim_nip')) {
            $data['nim_nip'] = '00000001';
        }

        User::updateOrCreate(
            ['email' => 'admin@akarasa.com'],
            $data
        );
    }
}