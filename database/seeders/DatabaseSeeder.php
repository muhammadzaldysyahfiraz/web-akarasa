<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');
        $generatedPassword = ! is_string($password) || $password === '';
        $password = $generatedPassword ? Str::password(24) : $password;
        $email = env('ADMIN_EMAIL', 'admin@kebuntoga.local');

        \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'nama' => 'Administrator',
                'password' => Hash::make($password),
            ],
        );

        if ($generatedPassword) {
            $this->command?->warn("Kredensial admin sementara (simpan dengan aman): {$email} / {$password}");
        }

        $this->call(TanamanSeeder::class);
    }
}
