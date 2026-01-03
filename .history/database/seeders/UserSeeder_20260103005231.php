<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin da empresa 1
        User::create([
            'company_id' => 1,
            'name' => 'Admin LogiTech',
            'email' => 'admin@logitech.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Manager da empresa 1
        User::create([
            'company_id' => 1,
            'name' => 'Gerente LogiTech',
            'email' => 'gerente@logitech.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Operador da empresa 2
        User::create([
            'company_id' => 2,
            'name' => 'Operador Expresso',
            'email' => 'operador@expresso.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);
    }
}
