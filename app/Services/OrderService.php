<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\OrderCreateDTO;
use App\DTO\OrderFilterDTO;
use App\Enums\OrderStatus;
use App\Models\Car;
use App\Models\Order;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Telegram\TelegramService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected DashboardService $dashboardService,
        protected UserRepositoryInterface $userRepository,
        protected TelegramService $telegramService,
    ) {}

    public function paginate(OrderFilterDTO $filter): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($filter);
    }

    public function getById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function create(OrderCreateDTO $dto): Order
    {
        $category = $this->categoryRepository->findById($dto->categoryId);
        if (! $category) {
            throw new InvalidArgumentException('Tanlangan yuvish kategoriyasi topilmadi.');
        }

        $normalizedPlate = Car::normalizePlateNumber($dto->plateNumber);
        $washer = $this->userRepository->findById($dto->washerId);

        $order = DB::transaction(function () use ($dto, $category, $washer, $normalizedPlate) {
            $order = $this->orderRepository->create([
                'car_id' => $dto->carId,
                'plate_number' => $normalizedPlate,
                'category_id' => $category->id,
                'operator_id' => $dto->operatorId,
                'washer_id' => $dto->washerId,
                'washer_name' => $washer?->name,
                'washer_phone' => $washer?->phone,
                'region_id' => $dto->regionId,
                'price' => $category->price,
                'status' => OrderStatus::NEW,
                'started_at' => null,
                'finished_at' => null,
            ]);

            $this->dashboardService->clearCache();

            return $order;
        });

        if ($washer && ! empty($washer->chat_id)) {
            try {
                $this->telegramService->sendOrderAssignedNotification($order, $washer);
            } catch (\Throwable $e) {
                Log::warning('Telegram notification error: ' . $e->getMessage());
            }
        }

        return $order;
    }

    public function updateStatus(int $id, OrderStatus $status): bool
    {
        $order = $this->orderRepository->findById($id);
        if (! $order) {
            return false;
        }

        if (! $order->status->canTransitionTo($status)) {
            Log::warning('Noto\'g\'ri status o\'tishi rad etildi', [
                'order_id' => $id,
                'current_status' => $order->status->value,
                'attempted_status' => $status->value,
            ]);

            return false;
        }

        return DB::transaction(function () use ($id, $status) {
            $updated = $this->orderRepository->updateStatus($id, $status);
            if ($updated) {
                $this->dashboardService->clearCache();
            }

            return $updated;
        });
    }
}
