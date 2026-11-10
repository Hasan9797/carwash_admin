<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = fake()->randomElement(OrderStatus::cases());
        $category = Category::factory()->create();
        $startedAt = $status !== OrderStatus::NEW ? fake()->dateTimeBetween('-7 days', 'now') : null;
        $finishedAt = $status === OrderStatus::DONE ? (clone $startedAt)->modify('+40 minutes') : null;

        return [
            'car_id' => null,
            'plate_number' => '01'.fake()->numerify('###').strtoupper(fake()->lexify('???')),
            'category_id' => $category->id,
            'operator_id' => User::factory()->operator(),
            'washer_id' => User::factory()->washer(),
            'region_id' => Region::factory(),
            'price' => $category->price,
            'status' => $status,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
        ];
    }
}
