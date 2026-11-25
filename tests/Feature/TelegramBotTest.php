<?php

declare(strict_types=1);

use App\DTO\OrderCreateDTO;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Services\OrderService;
use App\Services\Telegram\TelegramBotHandler;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('telegram.bot_token', 'test_dummy_token_12345');
    config()->set('telegram.webhook_secret', 'test_webhook_secret');

    $this->region = Region::create([
        'name_uz' => 'Yunusobod Filiali',
        'is_active' => true,
    ]);

    $this->category = Category::create([
        'name_uz' => 'VIP Yuvish',
        'price' => 100000,
        'is_active' => true,
    ]);

    $this->admin = User::create([
        'name' => 'Admin Bot Test',
        'email' => 'admin_bot@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::ADMIN,
        'chat_id' => '111222333',
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->operator = User::create([
        'name' => 'Operator Bot Test',
        'email' => 'operator_bot@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::OPERATOR,
        'chat_id' => '444555666',
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->washer = User::create([
        'name' => 'Yuvuvchi Bot Test',
        'email' => 'washer_bot@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::WASHER,
        'chat_id' => '777888999',
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);
});

test('unregistered telegram user gets chat_id response on /start', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $handler = app(TelegramBotHandler::class);
    $handler->handleUpdate([
        'message' => [
            'chat' => ['id' => 999000111],
            'text' => '/start',
        ],
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'sendMessage')
            && $request['chat_id'] === 999000111
            && str_contains($request['text'], "ro'yxatdan o'tmagansiz")
            && str_contains($request['text'], '999000111');
    });
});

test('registered admin receives admin menu on /start and can get reports', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $handler = app(TelegramBotHandler::class);

    // 1. /start
    $handler->handleUpdate([
        'message' => [
            'chat' => ['id' => 111222333],
            'text' => '/start',
        ],
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request['text'], 'Bosh Administrator');
    });

    // 2. Report request
    $handler->handleUpdate([
        'message' => [
            'chat' => ['id' => 111222333],
            'text' => '📅 Bugungi hisobot',
        ],
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request['text'], 'Bugungi hisobot')
            && str_contains($request['text'], 'Bajarilgan zakazlar');
    });
});

test('registered washer receives washer menu and can list active orders', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $order = Order::create([
        'plate_number' => '01W999WW',
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => $this->washer->id,
        'washer_name' => $this->washer->name,
        'washer_phone' => $this->washer->phone,
        'region_id' => $this->region->id,
        'price' => $this->category->price,
        'status' => OrderStatus::IN_PROGRESS,
    ]);

    $handler = app(TelegramBotHandler::class);

    $handler->handleUpdate([
        'message' => [
            'chat' => ['id' => 777888999],
            'text' => '🚗 Mening jarayondagi zakaslarim',
        ],
    ]);

    Http::assertSent(function ($request) use ($order) {
        return str_contains($request['text'], '01W999WW')
            && isset($request['reply_markup'])
            && str_contains($request['reply_markup'], "finish_order_{$order->id}");
    });
});

test('washer can complete order via callback query', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $order = Order::create([
        'plate_number' => '01K777KK',
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => $this->washer->id,
        'washer_name' => $this->washer->name,
        'washer_phone' => $this->washer->phone,
        'region_id' => $this->region->id,
        'price' => $this->category->price,
        'status' => OrderStatus::IN_PROGRESS,
    ]);

    $handler = app(TelegramBotHandler::class);

    $handler->handleUpdate([
        'callback_query' => [
            'id' => 'cb_12345',
            'from' => ['id' => 777888999],
            'data' => "finish_order_{$order->id}",
            'message' => [
                'chat' => ['id' => 777888999],
                'message_id' => 555,
            ],
        ],
    ]);

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::DONE)
        ->and($order->finished_at)->not->toBeNull();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'answerCallbackQuery')
            && str_contains($request['text'] ?? '', 'yakunlandi');
    });
});

test('washer cannot finish another washer order', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $otherWasher = User::create([
        'name' => 'Boshqa Yuvuvchi',
        'email' => 'other_washer@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::WASHER,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $order = Order::create([
        'plate_number' => '01X888XX',
        'category_id' => $this->category->id,
        'operator_id' => $this->operator->id,
        'washer_id' => $otherWasher->id,
        'washer_name' => $otherWasher->name,
        'region_id' => $this->region->id,
        'price' => $this->category->price,
        'status' => OrderStatus::IN_PROGRESS,
    ]);

    $handler = app(TelegramBotHandler::class);

    $handler->handleUpdate([
        'callback_query' => [
            'id' => 'cb_unauth_123',
            'from' => ['id' => 777888999], // $this->washer
            'data' => "finish_order_{$order->id}",
            'message' => [
                'chat' => ['id' => 777888999],
                'message_id' => 666,
            ],
        ],
    ]);

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::IN_PROGRESS);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'answerCallbackQuery')
            && str_contains($request['text'] ?? '', 'biriktirilmagan');
    });
});

test('webhook endpoint rejects requests with invalid secret token', function () {
    $response = $this->postJson(route('api.telegram.webhook'), ['update_id' => 1], [
        'X-Telegram-Bot-Api-Secret-Token' => 'wrong_secret',
    ]);

    $response->assertForbidden();
});

test('webhook endpoint accepts requests with valid secret token', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $response = $this->postJson(route('api.telegram.webhook'), [
        'update_id' => 101,
        'message' => [
            'chat' => ['id' => 111222333],
            'text' => '/start',
        ],
    ], [
        'X-Telegram-Bot-Api-Secret-Token' => 'test_webhook_secret',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);
});

test('order creation notifies washer if chat_id is present', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $orderService = app(OrderService::class);

    $dto = new OrderCreateDTO(
        plateNumber: '01N555NN',
        categoryId: $this->category->id,
        operatorId: $this->operator->id,
        washerId: $this->washer->id,
        regionId: $this->region->id,
        carId: null,
    );

    $orderService->create($dto);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'sendMessage')
            && (string) $request['chat_id'] === '777888999'
            && str_contains($request['text'], '01N555NN')
            && str_contains($request['text'], 'yangi zakaz biriktirildi');
    });
});
