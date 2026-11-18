<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\OrderFilterDTO;
use App\Enums\OrderStatus;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderStatusUpdateRequest;
use App\Services\CarService;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Services\RegionService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected CategoryService $categoryService,
        protected UserService $userService,
        protected CarService $carService,
        protected RegionService $regionService,
    ) {}

    public function index(Request $request): View
    {
        $statusInput = $request->input('status');
        $status = null;
        if ($statusInput !== null && $statusInput !== '') {
            if (is_numeric($statusInput)) {
                $status = OrderStatus::tryFrom((int) $statusInput);
            } else {
                $status = OrderStatus::fromName((string) $statusInput);
            }
        }

        $filter = new OrderFilterDTO(
            search: $request->input('search'),
            status: $status,
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            categoryId: $request->filled('category_id') ? (int) $request->input('category_id') : null,
            washerId: $request->filled('washer_id') ? (int) $request->input('washer_id') : null,
            regionId: $request->filled('region_id') ? (int) $request->input('region_id') : null,
            perPage: 15,
        );

        $orders = $this->orderService->paginate($filter);
        $categories = $this->categoryService->listActive();
        $washers = $this->userService->getActiveWashers();
        $regions = $this->regionService->listActive();

        return view('orders.index', [
            'orders' => $orders,
            'categories' => $categories,
            'washers' => $washers,
            'regions' => $regions,
            'statuses' => OrderStatus::cases(),
            'filter' => $filter,
        ]);
    }

    public function create(Request $request): View
    {
        $operator = $request->user();
        $categories = $this->categoryService->listActive();
        $washers = $this->userService->getActiveWashers($operator?->region_id);
        $recentCars = $this->carService->getRecentUnordered(20);
        $regions = $this->regionService->listActive();

        $categoriesPrices = $categories->pluck('price', 'id')->map(fn ($p) => (float) $p)->all();
        $recentCarsData = $recentCars->map(fn ($car) => [
            'id' => $car->id,
            'plate_number' => $car->plate_number,
            'camera_id' => $car->camera_id ?? 'Kamera',
            'time' => $car->detected_at?->format('H:i') ?? '',
        ])->values()->all();

        return view('orders.create', [
            'categories' => $categories,
            'washers' => $washers,
            'recentCars' => $recentCars,
            'recentCarsData' => $recentCarsData,
            'categoriesPrices' => $categoriesPrices,
            'regions' => $regions,
            'operatorRegion' => $operator?->region,
            'selectedCarId' => $request->query('car_id'),
        ]);
    }

    public function store(OrderCreateRequest $request): RedirectResponse
    {
        $dto = $request->toDto();
        $order = $this->orderService->create($dto);

        return redirect()->route('orders.index')->with('success', __('common.order_created', ['id' => $order->id]));
    }

    public function updateStatus(OrderStatusUpdateRequest $request, int $id): RedirectResponse
    {
        $updated = $this->orderService->updateStatus($id, $request->getStatus());

        if (! $updated) {
            return back()->with('error', __('common.order_status_failed'));
        }

        return back()->with('success', __('common.order_status_updated'));
    }
}
