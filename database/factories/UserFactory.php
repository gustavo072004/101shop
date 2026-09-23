<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_rol' => 1,
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'telefono' => '7'.fake()->numerify('#######'),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'Password123',
            'remember_token' => Str::random(10),
            'estado' => true,
        ];
    }
}
