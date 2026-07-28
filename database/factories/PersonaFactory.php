<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Persona>
 */
class PersonaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'   => fake('es_ES')->name(),
            'telefono' => fake()->numerify('7#######'),
            'email'    => fake()->unique()->safeEmail(),
            'activo'   => true,
        ];
    }
}
