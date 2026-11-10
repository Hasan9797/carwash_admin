<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\OrderStatus;

final readonly class OrderFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?OrderStatus $status = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $categoryId = null,
        public ?int $washerId = null,
        public ?int $regionId = null,
        public int $perPage = 15,
    ) {}
}
