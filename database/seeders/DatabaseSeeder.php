<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Function ini digunakan untuk mengisi data awal aplikasi,
     * termasuk akun admin dan data sistem pakar.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Admin Sistem Pakar',
                'is_admin' => true,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $this->call(ExpertSystemSeeder::class);
    }
}
