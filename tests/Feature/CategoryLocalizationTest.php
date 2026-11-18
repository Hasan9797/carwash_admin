<?php

declare(strict_types=1);

use App\DTO\ReportFilterDTO;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->region = Region::factory()->create(['name' => 'Test filiali']);
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
        'locale' => 'uz',
    ]);
});

test('categories table has name_uz, name_ru, name_crl columns and no json name column', function () {
    expect(Schema::hasColumn('categories', 'name_uz'))->toBeTrue();
    expect(Schema::hasColumn('categories', 'name_ru'))->toBeTrue();
    expect(Schema::hasColumn('categories', 'name_crl'))->toBeTrue();
    expect(Schema::hasColumn('categories', 'name'))->toBeFalse();
});

test('category name accessor dynamically resolves based on application locale with fallback to name_uz', function () {
    $category = Category::create([
        'name_uz' => 'Oddiy yuvish',
        'name_ru' => 'Обычная мойка',
        'name_crl' => 'Оддий ювиш',
        'price' => 50000,
        'is_active' => true,
    ]);

    app()->setLocale('uz');
    expect($category->name)->toBe('Oddiy yuvish');
    expect($category->title)->toBe('Oddiy yuvish');

    app()->setLocale('ru');
    expect($category->name)->toBe('Обычная мойка');
    expect($category->title)->toBe('Обычная мойка');

    app()->setLocale('uz_Cyrl');
    expect($category->name)->toBe('Оддий ювиш');
    expect($category->title)->toBe('Оддий ювиш');

    // Fallback when ru or crl is null/empty
    $fallbackCat = Category::create([
        'name_uz' => 'Faqat uzbekcha',
        'name_ru' => null,
        'name_crl' => '',
        'price' => 30000,
        'is_active' => true,
    ]);

    app()->setLocale('ru');
    expect($fallbackCat->name)->toBe('Faqat uzbekcha');
    expect($fallbackCat->title)->toBe('Faqat uzbekcha');

    app()->setLocale('uz_Cyrl');
    expect($fallbackCat->name)->toBe('Faqat uzbekcha');
    expect($fallbackCat->title)->toBe('Faqat uzbekcha');
});

test('category report title selects localized column at database query level', function () {
    $category = Category::create([
        'name_uz' => 'Kuzov tozalash',
        'name_ru' => 'Мойка кузова',
        'name_crl' => 'Кузов тозалаш',
        'price' => 60000,
        'is_active' => true,
    ]);

    $car = Car::factory()->create();
    Order::factory()->create([
        'car_id' => $car->id,
        'category_id' => $category->id,
        'region_id' => $this->region->id,
        'operator_id' => $this->admin->id,
        'washer_id' => $this->admin->id,
        'price' => 60000,
        'status' => OrderStatus::DONE->value,
        'finished_at' => now(),
    ]);

    $orderRepo = app(OrderRepositoryInterface::class);
    $filter = new ReportFilterDTO;

    // In Uzbek
    app()->setLocale('uz');
    $reportUz = $orderRepo->reportByCategory($filter);
    expect($reportUz->first()->title)->toBe('Kuzov tozalash');

    // In Russian
    app()->setLocale('ru');
    $reportRu = $orderRepo->reportByCategory($filter);
    expect($reportRu->first()->title)->toBe('Мойка кузова');

    // In Uzbek Cyrillic
    app()->setLocale('uz_Cyrl');
    $reportCrl = $orderRepo->reportByCategory($filter);
    expect($reportCrl->first()->title)->toBe('Кузов тозалаш');
});

test('category can be created and updated with name_uz, name_ru, name_crl via form', function () {
    $this->actingAs($this->admin)->post(route('categories.store'), [
        'name_uz' => 'Ekspress servis',
        'name_ru' => 'Экспресс сервис',
        'name_crl' => 'Экспресс сервис (крл)',
        'price' => 70000,
        'is_active' => 1,
    ])->assertRedirect(route('categories.index'));

    $created = Category::where('price', 70000)->first();
    expect($created)->not->toBeNull();
    expect($created->name_uz)->toBe('Ekspress servis');
    expect($created->name_ru)->toBe('Экспресс сервис');
    expect($created->name_crl)->toBe('Экспресс сервис (крл)');

    $this->actingAs($this->admin)->put(route('categories.update', $created->id), [
        'name_uz' => 'Ekspress servis yangi',
        'name_ru' => 'Экспресс сервис новый',
        'name_crl' => 'Экспресс сервис янги',
        'price' => 75000,
        'is_active' => 1,
    ])->assertRedirect(route('categories.index'));

    $updated = $created->fresh();
    expect($updated->name_uz)->toBe('Ekspress servis yangi');
    expect($updated->name_ru)->toBe('Экспресс сервис новый');
    expect($updated->name_crl)->toBe('Экспресс сервис янги');
    expect($updated->price)->toEqual('75000.00');
});
