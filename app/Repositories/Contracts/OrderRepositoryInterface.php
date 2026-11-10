<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTO\OrderFilterDTO;
use App\DTO\ReportFilterDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function paginate(OrderFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): ?Order;

    public function create(array $data): Order;

    public function updateStatus(int $id, OrderStatus $status): bool;

    public function countTodayWashed(): int;

    public function countAll(): int;

    public function countDone(): int;

    public function totalAmountDone(): float;

    public function getWeeklyCompletedOrders(): array;

    public function reportByCategory(ReportFilterDTO $filter): Collection;

    public function reportByRegion(ReportFilterDTO $filter): Collection;

    public function reportByOperator(ReportFilterDTO $filter): Collection;

    public function reportByWasher(ReportFilterDTO $filter): Collection;
}
