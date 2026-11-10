<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CarRepositoryInterface
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Car;

    public function findRecentByPlate(string $plateNumber, int $minutes = 2): ?Car;

    public function getRecentUnordered(int $limit = 15): Collection;

    public function create(array $data): Car;

    public function update(int $id, array $data): bool;

    public function count(): int;
}
