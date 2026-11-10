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
        return $this->categoryRepository->create([
            'name' => trim((string) $data['name']),
            'price' => (float) $data['price'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->categoryRepository->update($id, [
            'name' => trim((string) $data['name']),
            'price' => (float) $data['price'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
