<?php

declare(strict_types=1);

use App\DTO\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Services\DashboardService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Cache;

test('order service creates an order with category price snapshot and clears dashboard cache', function () {
    $region = Region::factory()->create();
    $operator = User::factory()->operator()->create(['region_id' => $region->id]);
    $washer = User::factory()->washer()->create(['region_id' => $region->id]);
    $category = Category::factory()->create(['price' => 85000.00]);

    // Dashboard keshiga qiymat yozamiz
    Cache::put(DashboardService::CACHE_KEY_STATS, ['dummy' => 123], 300);
    expect(Cache::has(DashboardService::CACHE_KEY_STATS))->toBeTrue();

    $orderService = app(OrderService::class);

    $dto = new OrderCreateDTO(
        plateNumber: '01 x 999 xx ',
        categoryId: $category->id,
        operatorId: $operator->id,
        washerId: $washer->id,
        regionId: $region->id,
    );

    $order = $orderService->create($dto);

    expect($order)->toBeInstanceOf(Order::class);
    expect($order->plate_number)->toBe('01X999XX');
    expect((float) $order->price)->toBe(85000.00);
    expect($order->status)->toBe(OrderStatus::NEW);
    expect($order->started_at)->toBeNull();
    expect($order->finished_at)->toBeNull();

    // Kategoriya narxi keyin o'zgarsa ham zakaz narxi snapshot bo'lib qolishi kerak
    $category->update(['price' => 100000.00]);
    expect((float) $order->fresh()->price)->toBe(85000.00);

    // Dashboard keshi tozalangan bo'lishi kerak
    expect(Cache::has(DashboardService::CACHE_KEY_STATS))->toBeFalse();
});

test('order service updates status properly and records timestamps', function () {
    $order = Order::factory()->create([
        'status' => OrderStatus::NEW,
        'started_at' => null,
        'finished_at' => null,
    ]);

    $orderService = app(OrderService::class);

    // NEW -> IN_PROGRESS
    $orderService->updateStatus($order->id, OrderStatus::IN_PROGRESS);
    $order->refresh();

    expect($order->status)->toBe(OrderStatus::IN_PROGRESS);
    expect($order->started_at)->not->toBeNull();
    expect($order->finished_at)->toBeNull();

    // IN_PROGRESS -> DONE
    $orderService->updateStatus($order->id, OrderStatus::DONE);
    $order->refresh();

    expect($order->status)->toBe(OrderStatus::DONE);
    expect($order->finished_at)->not->toBeNull();
});
