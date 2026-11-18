<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Region;
use App\Repositories\Contracts\RegionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RegionRepository implements RegionRepositoryInterface
{
    public function getAll(bool $onlyActive = false): Collection
    {
        $locale = app()->getLocale();

        return Region::query()
            ->when($onlyActive, fn ($q) => $q->where('is_active', true))
            ->orderBy("name->{$locale}")
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $locale = app()->getLocale();

        return Region::query()
            ->withCount('users')
            ->orderBy("name->{$locale}")
            ->paginate($perPage);
    }

    public function findById(int $id): ?Region
    {
        return Region::query()->find($id);
    }

    public function create(array $data): Region
    {
        return Region::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $region = $this->findById($id);

        return $region ? $region->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $region = $this->findById($id);

        return $region ? (bool) $region->delete() : false;
    }

    public function count(): int
    {
        return Region::query()->count();
    }
}
