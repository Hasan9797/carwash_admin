<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ReportFilterDTO;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
    ) {}

    public function getByCategory(ReportFilterDTO $filter): Collection
    {
        return $this->orderRepository->reportByCategory($filter);
    }

    public function getByRegion(ReportFilterDTO $filter): Collection
    {
        return $this->orderRepository->reportByRegion($filter);
    }

    public function getByOperator(ReportFilterDTO $filter): Collection
    {
        return $this->orderRepository->reportByOperator($filter);
    }

    public function getByWasher(ReportFilterDTO $filter): Collection
    {
        return $this->orderRepository->reportByWasher($filter);
    }
}
