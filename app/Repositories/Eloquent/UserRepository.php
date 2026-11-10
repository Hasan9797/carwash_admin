<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with('region')
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $search = trim((string) $filters['search']);
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['role']), function ($q) use ($filters) {
                $raw = $filters['role'];
                if (is_numeric($raw)) {
                    $q->where('role', (int) $raw);
                } elseif ($raw instanceof UserRole) {
                    $q->where('role', $raw->value);
                } elseif (is_string($raw) && ($enum = UserRole::fromName($raw)) !== null) {
                    $q->where('role', $enum->value);
                }
            })
            ->when(! empty($filters['region_id']), fn ($q) => $q->where('region_id', $filters['region_id']))
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::query()->with('region')->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function getActiveWashers(?int $regionId = null): Collection
    {
        return User::query()
            ->where('role', UserRole::WASHER->value)
            ->where('is_active', true)
            ->when($regionId !== null, fn ($q) => $q->where('region_id', $regionId))
            ->orderBy('name')
            ->get();
    }

    public function getActiveOperators(?int $regionId = null): Collection
    {
        return User::query()
            ->where('role', UserRole::OPERATOR->value)
            ->where('is_active', true)
            ->when($regionId !== null, fn ($q) => $q->where('region_id', $regionId))
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);

        return $user ? $user->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        return $user ? (bool) $user->delete() : false;
    }

    public function countByRole(UserRole $role): int
    {
        return User::query()->where('role', $role->value)->count();
    }
}
