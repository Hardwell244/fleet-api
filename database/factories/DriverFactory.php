<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->name(),
            'cpf' => $this->faker->numerify('###########'), // 11 dígitos
            'cnh' => $this->faker->numerify('#########'), // 9 dígitos
            'cnh_category' => $this->faker->randomElement(['A', 'B', 'AB', 'C', 'D', 'E']),
            'cnh_expires_at' => $this->faker->dateTimeBetween('now', '+5 years'),
            'phone' => $this->faker->numerify('###########'), // 11 dígitos
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
