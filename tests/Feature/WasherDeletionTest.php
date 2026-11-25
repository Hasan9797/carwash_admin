<?php

declare(strict_types=1);

use App\DTO\OrderCreateDTO;
use App\DTO\ReportFilterDTO;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;

beforeEach(function () {
    $this->region = Region::create([
        'name_uz' => 'Test Filial',
        'is_active' => true,
    ]);

    $this->category = Category::create([
        'name_uz' => 'Standart kuzov',
        'price' => 50000,
        'is_active' => true,
    ]);

    $this->admin = User::create([
        'name' => 'Bosh Admin',
        'email' => 'admin_test@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->operator = User::create([
        'name' => 'Operator Ali',
        'email' => 'operator_test@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::OPERATOR,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->washer = User::create([
        'name' => 'Yuvuvchi Vali',
        'email' => 'washer_test@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::WASHER,
        'phone' => '+998901234567',
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);
});

test('order creation saves washer_name and washer_phone snapshots', function () {
    $orderService = app(OrderService::class);

    $dto = new OrderCreateDTO(
        plateNumber: '01A111AA',
        categoryId: $this->category->id,
        operatorId: $this->operator->id,
        washerId: $this->washer->id,
        regionId: $this->region->id,
        carId: null,
    );

    $order = $orderService->create($dto);

    expect($order->washer_id)->toBe($this->washer->id)
        ->and($order->washer_name)->toBe('Yuvuvchi Vali')
        ->and($order->washer_phone)->toBe('+998901234567')
        ->and($order->washer_display_name)->toBe('Yuvuvchi Vali');
});

test('washer with orders can be deleted and order washer_id becomes null while snapshots remain', function () {
    $order = Order::create([
        'plate_number' => '01B222BB',
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => $this->washer->id,
        'washer_name' => $this->washer->name,
        'washer_phone' => $this->washer->phone,
        'region_id' => $this->region->id,
        'price' => $this->category->price,
        'status' => OrderStatus::DONE,
    ]);

    $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->washer->id));
    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $this->washer->id]);

    $order->refresh();
    expect($order->washer_id)->toBeNull()
        ->and($order->washer_name)->toBe('Yuvuvchi Vali')
        ->and($order->washer_phone)->toBe('+998901234567')
        ->and($order->washer_display_name)->toBe('Yuvuvchi Vali');
});

test('order list and car detail views render safely with deleted washer', function () {
    $car = Car::create([
        'plate_number' => '01C333CC',
        'camera_id' => 'CAM-1',
        'detected_at' => now(),
    ]);

    Order::create([
        'car_id' => $car->id,
        'plate_number' => $car->plate_number,
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => null,
        'washer_name' => 'Eski O\'chirilgan Yuvuvchi',
        'washer_phone' => '+998909998877',
        'region_id' => $this->region->id,
        'price' => $this->category->price,
        'status' => OrderStatus::DONE,
    ]);

    $responseOrders = $this->actingAs($this->admin)->get(route('orders.index'));
    $responseOrders->assertOk()
        ->assertSee('Eski O&#039;chirilgan Yuvuvchi', false);

    $responseCar = $this->actingAs($this->admin)->get(route('cars.show', $car->id));
    $responseCar->assertOk()
        ->assertSee('Eski O&#039;chirilgan Yuvuvchi', false);
});

test('washer report includes deleted washer orders via snapshot name', function () {
    Order::create([
        'plate_number' => '01D444DD',
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => null,
        'washer_name' => 'Nodir Yuvuvchi',
        'washer_phone' => '+998900001122',
        'region_id' => $this->region->id,
        'price' => 70000,
        'status' => OrderStatus::DONE,
    ]);

    $orderRepo = app(OrderRepositoryInterface::class);
    $report = $orderRepo->reportByWasher(new ReportFilterDTO());

    $found = $report->firstWhere('title', 'Nodir Yuvuvchi');
    expect($found)->not->toBeNull()
        ->and((int) $found->orders_count)->toBe(1)
        ->and((float) $found->total_amount)->toBe(70000.0);
});
