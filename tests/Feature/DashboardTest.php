<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

test('guest cannot access dashboard', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated admin can view dashboard with accurate statistics', function () {
    $region = Region::factory()->create();
    $admin = User::factory()->admin()->create(['region_id' => $region->id]);
    $operator = User::factory()->operator()->create(['region_id' => $region->id]);
    $washer = User::factory()->washer()->create(['region_id' => $region->id]);
    $category = Category::factory()->create(['price' => 50000.00]);

    // Bugun bajarilgan 2 ta zakaz
    Order::factory()->count(2)->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $category->id,
        'price' => 50000.00,
        'status' => OrderStatus::DONE,
        'finished_at' => Carbon::now(),
    ]);

    // Kecha bajarilgan 1 ta zakaz
    Order::factory()->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $category->id,
        'price' => 70000.00,
        'status' => OrderStatus::DONE,
        'finished_at' => Carbon::now()->subDay(),
    ]);

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertStatus(200)
        ->assertSee('Boshqaruv paneli')
        ->assertSee('Barcha zakazlar')
        ->assertSee('Bugun yuvilgan mashinalar')
        ->assertSee('Yuvuvchilar soni')
        ->assertSee('2 ta') // bugun 2 ta
        ->assertSee('170 000 so&#039;m', false); // 50k*2 + 70k = 170k

    // Keshda statistika saqlanganligini tekshiramiz
    expect(Cache::has(DashboardService::CACHE_KEY_STATS))->toBeTrue();
    $cached = Cache::get(DashboardService::CACHE_KEY_STATS);
    expect($cached['today_washed_count'])->toBe(2)
        ->and($cached['washers_count'])->toBe(1)
        ->and($cached['total_orders_count'])->toBe(3)
        ->and($cached['done_orders_count'])->toBe(3);
    expect((float) $cached['total_amount_done'])->toBe(170000.00);
});
