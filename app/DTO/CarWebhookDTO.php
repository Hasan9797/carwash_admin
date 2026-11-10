<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\CarbonInterface;

final readonly class CarWebhookDTO
{
    public function __construct(
        public string $plateNumber,
        public ?string $cameraId = null,
        public ?string $image = null,
        public ?CarbonInterface $detectedAt = null,
        public array $rawPayload = [],
    ) {}
}
