<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected function withFaker()
    {
        return \Faker\Factory::create('hu_HU');

    }
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('123'),

            // szerepkör (pl. 0 = user, 1 = admin)
            'role' => 2,

            'phone' => $this->faker->phoneNumber(),
            'city' => $this->faker->city(),
            'street' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'zip_code' => $this->faker->postcode(),

            // számlázási adatok (néha üres)
            'billing_phone' => $this->faker->optional()->phoneNumber(),
            'billing_city' => $this->faker->optional()->city(),
            'billing_street' => $this->faker->optional()->streetName(),
            'billing_house_number' => $this->faker->optional()->buildingNumber(),
            'billing_zip_code' => $this->faker->optional()->postcode(),

            
        ];
    }
}
