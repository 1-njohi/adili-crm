<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@adilirealestate.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+254700000000',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'is_permanent' => true,
            ]
        );
    }
}