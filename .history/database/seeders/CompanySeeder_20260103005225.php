<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'LogiTech Transportes',
            'cnpj' => '12345678000199',
            'email' => 'contato@logitech.com',
            'phone' => '11987654321',
            'is_active' => true,
        ]);

        Company::create([
            'name' => 'Expresso Rápido LTDA',
            'cnpj' => '98765432000188',
            'email' => 'contato@expresso.com',
            'phone' => '11912345678',
            'is_active' => true,
        ]);
    }
}
