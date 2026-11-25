<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters, $perPage);
    }

    public function getById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getActiveWashers(?int $regionId = null): Collection
    {
        return $this->userRepository->getActiveWashers($regionId);
    }

    public function getActiveOperators(?int $regionId = null): Collection
    {
        return $this->userRepository->getActiveOperators($regionId);
    }

    public function create(array $data): User
    {
        $payload = [
            'name' => trim((string) $data['name']),
            'email' => strtolower(trim((string) $data['email'])),
            'role' => $this->resolveRole($data['role'] ?? null),
            'phone' => ! empty($data['phone']) ? trim((string) $data['phone']) : null,
            'chat_id' => ! empty($data['chat_id']) ? trim((string) $data['chat_id']) : null,
            'region_id' => ! empty($data['region_id']) ? (int) $data['region_id'] : null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make((string) $data['password']);
        } elseif ($payload['role'] === UserRole::WASHER) {
            $payload['password'] = Hash::make('password');
        }

        return $this->userRepository->create($payload);
    }

    public function update(int $id, array $data): bool
    {
        $payload = [
            'name' => trim((string) $data['name']),
            'email' => strtolower(trim((string) $data['email'])),
            'role' => $this->resolveRole($data['role'] ?? null),
            'phone' => ! empty($data['phone']) ? trim((string) $data['phone']) : null,
            'chat_id' => ! empty($data['chat_id']) ? trim((string) $data['chat_id']) : null,
            'region_id' => ! empty($data['region_id']) ? (int) $data['region_id'] : null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make((string) $data['password']);
        }

        return $this->userRepository->update($id, $payload);
    }

    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    private function resolveRole(mixed $role): UserRole
    {
        if ($role instanceof UserRole) {
            return $role;
        }

        if (is_numeric($role)) {
            return UserRole::from((int) $role);
        }

        if (is_string($role)) {
            $enum = UserRole::fromName($role);
            if ($enum !== null) {
                return $enum;
            }
        }

        return UserRole::OPERATOR;
    }
}
