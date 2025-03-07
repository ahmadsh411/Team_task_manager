<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsFFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name"=>$this->faker->name,
            "email"=>$this->faker->email,
            "password"=>\Hash::make('123123123'),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ];
    }
}
