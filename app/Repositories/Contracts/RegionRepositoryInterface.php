<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Region;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RegionRepositoryInterface
{
    public function getAll(bool $onlyActive = false): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Region;

    public function create(array $data): Region;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function count(): int;
}
