<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Region;
use App\Repositories\Contracts\RegionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RegionService
{
    public function __construct(
        protected RegionRepositoryInterface $regionRepository,
    ) {}

    public function listActive(): Collection
    {
        return $this->regionRepository->getAll(onlyActive: true);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->regionRepository->paginate($perPage);
    }

    public function getById(int $id): ?Region
    {
        return $this->regionRepository->findById($id);
    }

    public function create(array $data): Region
    {
        return $this->regionRepository->create([
            'name' => trim((string) $data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->regionRepository->update($id, [
            'name' => trim((string) $data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->regionRepository->delete($id);
    }
}
