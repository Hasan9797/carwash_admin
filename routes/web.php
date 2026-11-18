<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Tilni almashtirish (GET /locale/{locale})
Route::get('/locale/{locale}', [LocalizationController::class, 'switch'])
    ->where('locale', 'uz|uz_Cyrl|ru')
    ->name('locale.switch');
Route::get('/lang/{locale}', [LocalizationController::class, 'switch'])
    ->where('locale', 'uz|uz_Cyrl|ru')
    ->name('lang.switch');

// Auth Routes (Session login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Panel Routes (Faqat tizimga kirgan Admin va Operatorlar)
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Operator ekrani: Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    // Kameradan o'tgan mashinalar ro'yxati va tafsiloti
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

    // Hisobotlar (Reports)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/category', [ReportController::class, 'category'])->name('category');
        Route::get('/category/export', [ReportController::class, 'exportCategory'])->name('category.export');

        Route::get('/region', [ReportController::class, 'region'])->name('region');
        Route::get('/region/export', [ReportController::class, 'exportRegion'])->name('region.export');

        Route::get('/operator', [ReportController::class, 'operator'])->name('operator');
        Route::get('/operator/export', [ReportController::class, 'exportOperator'])->name('operator.export');

        Route::get('/washer', [ReportController::class, 'washer'])->name('washer');
        Route::get('/washer/export', [ReportController::class, 'exportWasher'])->name('washer.export');
    });

    // Faqat Administrator uchun ruxsat etilgan CRUD'lar
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('regions', RegionController::class);
    });
});
