<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;

test('report by category calculates group totals at database level', function () {
    $region = Region::factory()->create();
    $admin = User::factory()->admin()->create(['region_id' => $region->id]);
    $operator = User::factory()->operator()->create(['region_id' => $region->id]);
    $washer = User::factory()->washer()->create(['region_id' => $region->id]);

    $catVip = Category::factory()->create(['name' => 'VIP', 'price' => 100000]);
    $catStandard = Category::factory()->create(['name' => 'Standard', 'price' => 50000]);

    // 2 ta VIP zakaz (DONE)
    Order::factory()->count(2)->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $catVip->id,
        'price' => 100000,
        'status' => OrderStatus::DONE,
    ]);

    // 1 ta Standard zakaz (DONE)
    Order::factory()->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $catStandard->id,
        'price' => 50000,
        'status' => OrderStatus::DONE,
    ]);

    // 1 ta NEW zakaz (hisobotga kirmasligi kerak)
    Order::factory()->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $catVip->id,
        'price' => 100000,
        'status' => OrderStatus::NEW,
    ]);

    $response = $this->actingAs($admin)->get('/reports/category');

    $response->assertStatus(200)
        ->assertSee('VIP')
        ->assertSee('Standard')
        ->assertSee('250 000 so&#039;m', false); // 100k*2 + 50k = 250k
});

test('report by region executes properly and groups without sql json errors', function () {
    $region = Region::factory()->create(['name_uz' => 'Yunusobod markazi']);
    $admin = User::factory()->admin()->create(['region_id' => $region->id]);
    $operator = User::factory()->operator()->create(['region_id' => $region->id]);
    $washer = User::factory()->washer()->create(['region_id' => $region->id]);
    $cat = Category::factory()->create(['price' => 80000]);

    Order::factory()->count(2)->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $cat->id,
        'price' => 80000,
        'status' => OrderStatus::DONE,
    ]);

    $response = $this->actingAs($admin)->get('/reports/region');

    $response->assertStatus(200)
        ->assertSee('Yunusobod markazi')
        ->assertSee('160 000 so&#039;m', false);

    $exportResponse = $this->actingAs($admin)->get('/reports/region/export');
    $exportResponse->assertStatus(200)
        ->assertHeader('content-disposition');
});

test('report by operator and washer execute without sql grouping errors', function () {
    $region = Region::factory()->create(['name_uz' => 'Chilonzor markazi']);
    $admin = User::factory()->admin()->create(['region_id' => $region->id]);
    $operator = User::factory()->operator()->create(['name' => 'Akmal Operator', 'region_id' => $region->id]);
    $washer = User::factory()->washer()->create(['name' => 'Bobur Yuvuvchi', 'region_id' => $region->id]);
    $cat = Category::factory()->create(['price' => 45000]);

    Order::factory()->create([
        'region_id' => $region->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'category_id' => $cat->id,
        'price' => 45000,
        'status' => OrderStatus::DONE,
    ]);

    $this->actingAs($admin)->get('/reports/operator')
        ->assertStatus(200)
        ->assertSee('Akmal Operator')
        ->assertSee('Chilonzor markazi');

    $this->actingAs($admin)->get('/reports/washer')
        ->assertStatus(200)
        ->assertSee('Bobur Yuvuvchi')
        ->assertSee('Chilonzor markazi');
});

test('report excel export returns a downloadable spreadsheet', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/reports/category/export');

    $response->assertStatus(200)
        ->assertHeader('content-disposition');
});
