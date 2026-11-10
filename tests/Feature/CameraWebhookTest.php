<?php

declare(strict_types=1);

use App\Jobs\ProcessCameraImageJob;
use App\Models\Car;
use Illuminate\Support\Facades\Queue;

test('camera webhook rejects requests with missing or invalid token', function () {
    config(['services.camera.webhook_token' => 'test-secret-token']);

    // Headersiz so'rov
    $response = $this->postJson('/api/webhooks/camera', [
        'plate_number' => '01A777AA',
    ]);
    $response->assertStatus(401);

    // Noto'g'ri token bilan
    $response = $this->withHeader('X-Webhook-Token', 'wrong-token')
        ->postJson('/api/webhooks/camera', [
            'plate_number' => '01A777AA',
        ]);
    $response->assertStatus(401);
});

test('camera webhook registers new car with valid token and dispatches image job', function () {
    Queue::fake();
    config(['services.camera.webhook_token' => 'test-secret-token']);

    $response = $this->withHeader('X-Webhook-Token', 'test-secret-token')
        ->postJson('/api/webhooks/camera', [
            'plate_number' => '01 a 777 aa ',
            'camera_id' => 'CAM-01',
            'image' => 'https://example.com/car.jpg',
            'detected_at' => now()->toIso8601String(),
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'plate_number' => '01A777AA',
                'is_new' => true,
            ],
        ]);

    expect(Car::count())->toBe(1);
    $car = Car::first();
    expect($car->plate_number)->toBe('01A777AA');
    expect($car->camera_id)->toBe('CAM-01');

    Queue::assertPushed(ProcessCameraImageJob::class);
});

test('camera webhook is idempotent and prevents duplicate entry within 2 minutes', function () {
    config(['services.camera.webhook_token' => 'test-secret-token']);

    // 1-so'rov
    $firstResponse = $this->withHeader('X-Webhook-Token', 'test-secret-token')
        ->postJson('/api/webhooks/camera', [
            'plate_number' => '10B123BB',
            'camera_id' => 'CAM-02',
        ]);
    $firstResponse->assertStatus(201);
    expect(Car::count())->toBe(1);

    // 1 daqiqadan keyin yana shu raqam kelsa
    $secondResponse = $this->withHeader('X-Webhook-Token', 'test-secret-token')
        ->postJson('/api/webhooks/camera', [
            'plate_number' => '10b123bb',
            'camera_id' => 'CAM-02',
        ]);

    $secondResponse->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => [
                'plate_number' => '10B123BB',
                'is_new' => false,
            ],
        ]);

    // Baza faqat 1 ta yozuvni saqlashi shart
    expect(Car::count())->toBe(1);
});
