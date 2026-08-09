<?php

namespace Database\Factories;

use App\Enums\EventType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $initDate = fake()->dateTimeBetween('-1 month', '+6 months');
        $endDate = (clone $initDate)->modify('+'.fake()->numberBetween(0, 3).' days');

        $names = [
            'Casamiento '.fake()->lastName().' & '.fake()->lastName(),
            'Cumpleaños de '.fake()->firstName(),
            'Graduación '.fake()->company(),
            'Evento corporativo '.fake()->company(),
            'Fiesta privada '.fake()->lastName(),
        ];

        return [
            'name' => fake()->randomElement($names),
            'init_date' => $initDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'type' => fake()->randomElement(EventType::cases()),
        ];
    }
}
