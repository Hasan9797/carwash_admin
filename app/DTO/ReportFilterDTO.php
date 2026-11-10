<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class ReportFilterDTO
{
    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $regionId = null,
    ) {}
}
