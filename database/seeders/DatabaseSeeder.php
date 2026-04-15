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
     * Seed the application's database.
     */
   public function run(): void
{
    User::updateOrCreate(
        ['email' => 'superadmin.admin@sumberprimamandiri.com'],
        [
            'nama' => 'Super Admin',
            'password_hash' => Hash::make('superadmin123'),
            'role' => 'admin'
        ]
    );
}
}
