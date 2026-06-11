<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'PT ' . $this->faker->company,
            'brand_name' => $this->faker->companySuffix,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'siskohat_code' => $this->faker->numerify('#####'),
            'travel_license_no' => 'SK.' . $this->faker->numerify('###/PPIU/####'),
        ];
    }
}
