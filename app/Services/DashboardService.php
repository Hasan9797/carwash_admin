<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRole;
use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public const string CACHE_KEY_STATS = 'dashboard_stats';

    public const string CACHE_KEY_CHART = 'dashboard_chart_weekly';

    public const int CACHE_TTL_SECONDS = 300; // 5 daqiqa

    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected UserRepositoryInterface $userRepository,
        protected CarRepositoryInterface $carRepository,
    ) {}

    /**
     * @return array{
     *     operators_count: int,
     *     admins_count: int,
     *     washers_count: int,
     *     total_orders_count: int,
     *     done_orders_count: int,
     *     today_washed_count: int,
     *     total_amount_done: float
     * }
     */
    public function getStatistics(): array
    {
        return Cache::remember(self::CACHE_KEY_STATS, self::CACHE_TTL_SECONDS, function (): array {
            return [
                'operators_count' => $this->userRepository->countByRole(UserRole::OPERATOR),
                'admins_count' => $this->userRepository->countByRole(UserRole::ADMIN),
                'washers_count' => $this->userRepository->countByRole(UserRole::WASHER),
                'total_orders_count' => $this->orderRepository->countAll(),
                'done_orders_count' => $this->orderRepository->countDone(),
                'today_washed_count' => $this->orderRepository->countTodayWashed(),
                'total_amount_done' => $this->orderRepository->totalAmountDone(),
            ];
        });
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    public function getWeeklyChartData(): array
    {
        return Cache::remember(self::CACHE_KEY_CHART, self::CACHE_TTL_SECONDS, function (): array {
            return $this->orderRepository->getWeeklyCompletedOrders();
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATS);
        Cache::forget(self::CACHE_KEY_CHART);
    }
}
