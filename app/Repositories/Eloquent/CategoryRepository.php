<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(bool $onlyActive = false): Collection
    {
        return Category::query()
            ->when($onlyActive, fn ($q) => $q->where('is_active', true))
            ->orderBy('price')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Category::query()
            ->orderBy('price')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Category
    {
        return Category::query()->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = $this->findById($id);

        return $category ? $category->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);

        return $category ? (bool) $category->delete() : false;
    }

    public function count(): int
    {
        return Category::query()->count();
    }
}
