<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function listActive(): Collection
    {
        return $this->categoryRepository->getAll(onlyActive: true);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    public function getById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function create(array $data): Category
    {
        $payload = [
            'price' => (float) $data['price'],
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

        if (array_key_exists('description', $data)) {
            $payload['description'] = is_array($data['description'])
                ? array_map(fn ($v) => trim((string) $v), $data['description'])
                : (is_string($data['description']) ? trim($data['description']) : null);
        }

        return $this->categoryRepository->create($payload);
    }

    public function update(int $id, array $data): bool
    {
        $payload = [
            'price' => (float) $data['price'],
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

        if (array_key_exists('description', $data)) {
            $payload['description'] = is_array($data['description'])
                ? array_map(fn ($v) => trim((string) $v), $data['description'])
                : (is_string($data['description']) ? trim($data['description']) : null);
        }

        return $this->categoryRepository->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
