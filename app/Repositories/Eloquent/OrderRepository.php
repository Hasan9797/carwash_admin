<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTO\OrderFilterDTO;
use App\DTO\ReportFilterDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginate(OrderFilterDTO $filter): LengthAwarePaginator
    {
        return Order::query()
            ->with(['category', 'operator', 'washer', 'region', 'car'])
            ->when(! empty($filter->search), function ($q) use ($filter) {
                $term = trim((string) $filter->search);
                $q->where('plate_number', 'like', "%{$term}%");
            })
            ->when($filter->status !== null, fn ($q) => $q->where('status', $filter->status->value))
            ->when(! empty($filter->dateFrom), fn ($q) => $q->whereDate('created_at', '>=', $filter->dateFrom))
            ->when(! empty($filter->dateTo), fn ($q) => $q->whereDate('created_at', '<=', $filter->dateTo))
            ->when(! empty($filter->categoryId), fn ($q) => $q->where('category_id', $filter->categoryId))
            ->when(! empty($filter->washerId), fn ($q) => $q->where('washer_id', $filter->washerId))
            ->when(! empty($filter->regionId), fn ($q) => $q->where('region_id', $filter->regionId))
            ->orderBy('id', 'desc')
            ->paginate($filter->perPage);
    }

    public function findById(int $id): ?Order
    {
        return Order::query()
            ->with(['category', 'operator', 'washer', 'region', 'car'])
            ->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function updateStatus(int $id, OrderStatus $status): bool
    {
        $order = Order::query()->find($id);
        if (! $order) {
            return false;
        }

        $updates = ['status' => $status];

        if ($status === OrderStatus::IN_PROGRESS && $order->started_at === null) {
            $updates['started_at'] = Carbon::now();
        } elseif ($status === OrderStatus::DONE && $order->finished_at === null) {
            $updates['finished_at'] = Carbon::now();
            if ($order->started_at === null) {
                $updates['started_at'] = Carbon::now();
            }
        }

        return (bool) $order->update($updates);
    }

    public function countTodayWashed(): int
    {
        return Order::query()
            ->where('status', OrderStatus::DONE->value)
            ->whereDate('finished_at', Carbon::today())
            ->count();
    }

    public function countAll(): int
    {
        return Order::query()->count();
    }

    public function countDone(): int
    {
        return Order::query()
            ->where('status', OrderStatus::DONE->value)
            ->count();
    }

    public function totalAmountDone(): float
    {
        return (float) Order::query()
            ->where('status', OrderStatus::DONE->value)
            ->sum('price');
    }

    public function getWeeklyCompletedOrders(): array
    {
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $records = Order::query()
            ->selectRaw('DATE(finished_at) as wash_date, COUNT(*) as count')
            ->where('status', OrderStatus::DONE->value)
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->groupBy('wash_date')
            ->pluck('count', 'wash_date')
            ->all();

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->toDateString();
            $labels[] = $date->locale('uz')->isoFormat('DD MMM');
            $values[] = (int) ($records[$dateString] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function reportByCategory(ReportFilterDTO $filter): Collection
    {
        $query = DB::table('orders')
            ->join('categories', 'orders.category_id', '=', 'categories.id')
            ->select([
                'categories.id',
                'categories.name as title',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.price), 0) as total_amount'),
                DB::raw('COALESCE(AVG(orders.price), 0) as average_amount'),
            ])
            ->where('orders.status', OrderStatus::DONE->value);

        $this->applyReportFilters($query, $filter);

        return $query->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function reportByRegion(ReportFilterDTO $filter): Collection
    {
        $query = DB::table('orders')
            ->join('regions', 'orders.region_id', '=', 'regions.id')
            ->select([
                'regions.id',
                'regions.name as title',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.price), 0) as total_amount'),
                DB::raw('COALESCE(AVG(orders.price), 0) as average_amount'),
            ])
            ->where('orders.status', OrderStatus::DONE->value);

        $this->applyReportFilters($query, $filter);

        return $query->groupBy('regions.id', 'regions.name')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function reportByOperator(ReportFilterDTO $filter): Collection
    {
        $query = DB::table('orders')
            ->join('users', 'orders.operator_id', '=', 'users.id')
            ->leftJoin('regions', 'orders.region_id', '=', 'regions.id')
            ->select([
                'users.id',
                'users.name as title',
                'regions.name as region_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.price), 0) as total_amount'),
                DB::raw('COALESCE(AVG(orders.price), 0) as average_amount'),
            ])
            ->where('orders.status', OrderStatus::DONE->value);

        $this->applyReportFilters($query, $filter);

        return $query->groupBy('users.id', 'users.name', 'regions.name')
            ->orderByDesc('total_amount')
            ->get();
    }

    public function reportByWasher(ReportFilterDTO $filter): Collection
    {
        $query = DB::table('orders')
            ->join('users', 'orders.washer_id', '=', 'users.id')
            ->leftJoin('regions', 'orders.region_id', '=', 'regions.id')
            ->select([
                'users.id',
                'users.name as title',
                'regions.name as region_name',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.price), 0) as total_amount'),
                DB::raw('COALESCE(AVG(orders.price), 0) as average_amount'),
            ])
            ->where('orders.status', OrderStatus::DONE->value);

        $this->applyReportFilters($query, $filter);

        return $query->groupBy('users.id', 'users.name', 'regions.name')
            ->orderByDesc('total_amount')
            ->get();
    }

    private function applyReportFilters($query, ReportFilterDTO $filter): void
    {
        if (! empty($filter->dateFrom)) {
            $query->whereDate('orders.created_at', '>=', $filter->dateFrom);
        }
        if (! empty($filter->dateTo)) {
            $query->whereDate('orders.created_at', '<=', $filter->dateTo);
        }
        if (! empty($filter->regionId)) {
            $query->where('orders.region_id', $filter->regionId);
        }
    }
}
