<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name_uz' => fake()->randomElement(['Standart yuvish', 'Kuzov + Salon', 'Kompleks VIP', 'Ekspress']),
            'name_ru' => null,
            'name_crl' => null,
            'price' => fake()->randomElement([50000, 80000, 120000, 180000]),
            'is_active' => true,
        ];
    }
}
