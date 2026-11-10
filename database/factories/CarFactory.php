<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        $regionCode = fake()->randomElement(['01', '10', '30', '40', '60', '80']);
        $letters = strtoupper(fake()->lexify('???'));
        $numbers = fake()->numerify('###');
        $plate = "{$regionCode}{$numbers}{$letters}";

        return [
            'plate_number' => $plate,
            'camera_id' => 'CAM-'.fake()->numberBetween(1, 5),
            'image_path' => null,
            'detected_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'raw_payload' => [
                'plate_number' => $plate,
                'camera_id' => 'CAM-'.fake()->numberBetween(1, 5),
                'confidence' => 0.98,
            ],
        ];
    }
}
