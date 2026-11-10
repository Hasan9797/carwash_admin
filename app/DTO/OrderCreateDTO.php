<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class OrderCreateDTO
{
    public function __construct(
        public string $plateNumber,
        public int $categoryId,
        public int $operatorId,
        public int $washerId,
        public int $regionId,
        public ?int $carId = null,
    ) {}
}
