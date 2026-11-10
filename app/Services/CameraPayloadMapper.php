<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CarWebhookDTO;
use Carbon\Carbon;
use InvalidArgumentException;

class CameraPayloadMapper
{
    /**
     * Map request payload to CarWebhookDTO
     *
     * @param  array<string, mixed>  $payload
     */
    public function map(array $payload): CarWebhookDTO
    {
        $plate = $payload['plate_number']
            ?? $payload['plate']
            ?? $payload['license_plate']
            ?? $payload['car_number']
            ?? null;

        if (empty($plate) || ! is_string($plate)) {
            throw new InvalidArgumentException("Avtomobil davlat raqami ko'rsatilmagan (plate_number majburiy).");
        }

        $cameraId = $payload['camera_id']
            ?? $payload['cameraId']
            ?? $payload['device_id']
            ?? null;

        $image = $payload['image']
            ?? $payload['image_url']
            ?? $payload['photo']
            ?? $payload['base64_image']
            ?? null;

        $rawTimestamp = $payload['detected_at']
            ?? $payload['timestamp']
            ?? $payload['time']
            ?? null;

        $detectedAt = null;
        if (! empty($rawTimestamp)) {
            try {
                $detectedAt = Carbon::parse($rawTimestamp);
            } catch (\Throwable) {
                $detectedAt = Carbon::now();
            }
        } else {
            $detectedAt = Carbon::now();
        }

        return new CarWebhookDTO(
            plateNumber: trim($plate),
            cameraId: ! empty($cameraId) ? (string) $cameraId : null,
            image: ! empty($image) ? (string) $image : null,
            detectedAt: $detectedAt,
            rawPayload: $payload,
        );
    }
}
