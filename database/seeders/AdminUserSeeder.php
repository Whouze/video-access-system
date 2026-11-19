<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('role_name', 'admin')->first();
        $customer = Role::where('role_name', 'customer')->first();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role_id' => $admin->id,
            ]
        );

        // akun demo supaya penilai bisa langsung mencoba alur customer
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer Demo',
                'password' => Hash::make('password'),
                'role_id' => $customer->id,
            ]
        );
    }
}