<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CarService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarController extends Controller
{
    public function __construct(
        protected CarService $carService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $cars = $this->carService->paginate($search, 15);

        return view('cars.index', [
            'cars' => $cars,
            'search' => $search,
        ]);
    }

    public function show(int $id): View
    {
        $car = $this->carService->getById($id);
        if (! $car) {
            abort(404, 'Avtomobil topilmadi.');
        }

        return view('cars.show', [
            'car' => $car,
        ]);
    }
}
