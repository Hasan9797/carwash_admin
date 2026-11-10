<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('user role enum values and translations are correct', function () {
    expect(UserRole::ADMIN->value)->toBe(1)
        ->and(UserRole::OPERATOR->value)->toBe(2)
        ->and(UserRole::WASHER->value)->toBe(3);

    expect(UserRole::ADMIN->label('uz'))->toBe('Admin')
        ->and(UserRole::ADMIN->label('en'))->toBe('Admin')
        ->and(UserRole::ADMIN->label('ru'))->toBe('Администратор');

    expect(UserRole::OPERATOR->label('uz'))->toBe('Operator')
        ->and(UserRole::OPERATOR->label('en'))->toBe('Operator')
        ->and(UserRole::OPERATOR->label('ru'))->toBe('Оператор');

    expect(UserRole::WASHER->label('uz'))->toBe('Yuvuvchi')
        ->and(UserRole::WASHER->label('en'))->toBe('Washer')
        ->and(UserRole::WASHER->label('ru'))->toBe('Мойщик');

    expect(UserRole::labelFor(1, 'ru'))->toBe('Администратор')
        ->and(UserRole::getLabel(3, 'uz'))->toBe('Yuvuvchi')
        ->and(UserRole::labels('uz')[2])->toBe('Operator');
});

test('order status enum values and translations are correct', function () {
    expect(OrderStatus::NEW->value)->toBe(1)
        ->and(OrderStatus::IN_PROGRESS->value)->toBe(2)
        ->and(OrderStatus::DONE->value)->toBe(3)
        ->and(OrderStatus::CANCELLED->value)->toBe(4);

    expect(OrderStatus::NEW->label('uz'))->toBe('Yangi')
        ->and(OrderStatus::NEW->label('en'))->toBe('New')
        ->and(OrderStatus::NEW->label('ru'))->toBe('Новый');

    expect(OrderStatus::IN_PROGRESS->label('uz'))->toBe('Jarayonda')
        ->and(OrderStatus::IN_PROGRESS->label('en'))->toBe('In Progress')
        ->and(OrderStatus::IN_PROGRESS->label('ru'))->toBe('В процессе');

    expect(OrderStatus::DONE->label('uz'))->toBe('Bajarildi')
        ->and(OrderStatus::DONE->label('en'))->toBe('Done')
        ->and(OrderStatus::DONE->label('ru'))->toBe('Завершен');

    expect(OrderStatus::CANCELLED->label('uz'))->toBe('Bekor qilindi')
        ->and(OrderStatus::CANCELLED->label('en'))->toBe('Cancelled')
        ->and(OrderStatus::CANCELLED->label('ru'))->toBe('Отменен');

    expect(OrderStatus::labelFor(2, 'uz'))->toBe('Jarayonda')
        ->and(OrderStatus::getLabel(3, 'ru'))->toBe('Завершен')
        ->and(OrderStatus::labels('en')[4])->toBe('Cancelled');
});

test('database stores integers for user role and order status', function () {
    $region = Region::create(['name' => 'Test Region', 'is_active' => true]);
    $cat = Category::create(['name' => 'Test Cat', 'price' => 50000, 'is_active' => true]);

    $operator = User::create([
        'name' => 'Enum Operator',
        'email' => 'enum_op@example.com',
        'password' => 'password',
        'role' => UserRole::OPERATOR,
        'region_id' => $region->id,
        'is_active' => true,
    ]);

    $washer = User::create([
        'name' => 'Enum Washer',
        'email' => 'enum_wash@example.com',
        'password' => 'password',
        'role' => UserRole::WASHER,
        'region_id' => $region->id,
        'is_active' => true,
    ]);

    $order = Order::create([
        'plate_number' => '01A123BC',
        'category_id' => $cat->id,
        'operator_id' => $operator->id,
        'washer_id' => $washer->id,
        'region_id' => $region->id,
        'price' => $cat->price,
        'status' => OrderStatus::NEW,
    ]);

    $rawUser = DB::table('users')->where('id', $operator->id)->first();
    $rawOrder = DB::table('orders')->where('id', $order->id)->first();

    expect((int) $rawUser->role)->toBe(2);
    expect((int) $rawOrder->status)->toBe(1);

    $freshOrder = Order::find($order->id);
    expect($freshOrder->status)->toBe(OrderStatus::NEW)
        ->and($freshOrder->status->label('uz'))->toBe('Yangi');
});
