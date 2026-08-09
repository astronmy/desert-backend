<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    protected $model = Guest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_number' => (string) fake()->unique()->numberBetween(10000000, 49999999),
            'id_type' => DocumentType::Dni,
        ];
    }
}
