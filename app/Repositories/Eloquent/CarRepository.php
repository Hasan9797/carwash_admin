<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CarRepository implements CarRepositoryInterface
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Car::query()
            ->with('latestOrder')
            ->when(! empty($search), function ($q) use ($search) {
                $normalized = Car::normalizePlateNumber($search);
                $q->where('plate_number', 'like', "%{$normalized}%");
            })
            ->orderBy('detected_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Car
    {
        return Car::query()->with(['orders.category', 'orders.operator', 'orders.washer'])->find($id);
    }

    public function findRecentByPlate(string $plateNumber, int $minutes = 2): ?Car
    {
        $normalized = Car::normalizePlateNumber($plateNumber);
        $since = Carbon::now()->subMinutes($minutes);

        return Car::query()
            ->where('plate_number', $normalized)
            ->where('detected_at', '>=', $since)
            ->latest('detected_at')
            ->first();
    }

    public function getRecentUnordered(int $limit = 15): Collection
    {
        // Oxirgi 24 soatda kelgan va hali zakazga ulanmagan mashinalar
        return Car::query()
            ->whereDoesntHave('orders')
            ->where('detected_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('detected_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Car
    {
        return Car::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $car = Car::query()->find($id);

        return $car ? $car->update($data) : false;
    }

    public function count(): int
    {
        return Car::query()->count();
    }
}
