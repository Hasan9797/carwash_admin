<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CameraWebhookController;
use App\Http\Middleware\VerifyCameraWebhookToken;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/camera', [CameraWebhookController::class, 'handle'])
    ->middleware([
        VerifyCameraWebhookToken::class,
        'throttle:60,1',
    ])
    ->name('api.webhooks.camera');
