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
        $payload = [
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (isset($data['name_uz'])) {
            $payload['name_uz'] = trim((string) $data['name_uz']);
            $payload['name_ru'] = ! empty($data['name_ru']) ? trim((string) $data['name_ru']) : null;
            $payload['name_crl'] = ! empty($data['name_crl'])
                ? trim((string) $data['name_crl'])
                : (! empty($data['name_uz_Cyrl']) ? trim((string) $data['name_uz_Cyrl']) : null);
        } elseif (isset($data['name']) && is_array($data['name'])) {
            $payload['name_uz'] = trim((string) ($data['name']['uz'] ?? ''));
            $payload['name_ru'] = ! empty($data['name']['ru']) ? trim((string) $data['name']['ru']) : null;
            $payload['name_crl'] = ! empty($data['name']['uz_Cyrl'])
                ? trim((string) $data['name']['uz_Cyrl'])
                : (! empty($data['name']['crl']) ? trim((string) $data['name']['crl']) : null);
        } elseif (isset($data['name'])) {
            $payload['name_uz'] = trim((string) $data['name']);
        }

        return $this->regionRepository->create($payload);
    }

    public function update(int $id, array $data): bool
    {
        $payload = [
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (isset($data['name_uz'])) {
            $payload['name_uz'] = trim((string) $data['name_uz']);
            $payload['name_ru'] = ! empty($data['name_ru']) ? trim((string) $data['name_ru']) : null;
            $payload['name_crl'] = ! empty($data['name_crl'])
                ? trim((string) $data['name_crl'])
                : (! empty($data['name_uz_Cyrl']) ? trim((string) $data['name_uz_Cyrl']) : null);
        } elseif (isset($data['name']) && is_array($data['name'])) {
            $payload['name_uz'] = trim((string) ($data['name']['uz'] ?? ''));
            $payload['name_ru'] = ! empty($data['name']['ru']) ? trim((string) $data['name']['ru']) : null;
            $payload['name_crl'] = ! empty($data['name']['uz_Cyrl'])
                ? trim((string) $data['name']['uz_Cyrl'])
                : (! empty($data['name']['crl']) ? trim((string) $data['name']['crl']) : null);
        } elseif (isset($data['name'])) {
            $payload['name_uz'] = trim((string) $data['name']);
        }

        return $this->regionRepository->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->regionRepository->delete($id);
    }
}
