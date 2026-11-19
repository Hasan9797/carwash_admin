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
    $this->region = Region::factory()->create(['name_uz' => 'Test filiali', 'name_ru' => 'Тест филиал', 'name_crl' => 'Тест филиали']);
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
        'locale' => 'uz',
    ]);
});

test('regions table has name_uz, name_ru, name_crl columns and no json name column', function () {
    expect(Schema::hasColumn('regions', 'name_uz'))->toBeTrue();
    expect(Schema::hasColumn('regions', 'name_ru'))->toBeTrue();
    expect(Schema::hasColumn('regions', 'name_crl'))->toBeTrue();
    expect(Schema::hasColumn('regions', 'name'))->toBeFalse();
});

test('region name accessor dynamically resolves based on application locale with fallback to name_uz', function () {
    $region = Region::create([
        'name_uz' => 'Yunusobod filiali',
        'name_ru' => 'Юнусабадский филиал',
        'name_crl' => 'Юнусобод филиали',
        'is_active' => true,
    ]);

    app()->setLocale('uz');
    expect($region->name)->toBe('Yunusobod filiali');
    expect($region->title)->toBe('Yunusobod filiali');

    app()->setLocale('ru');
    expect($region->name)->toBe('Юнусабадский филиал');
    expect($region->title)->toBe('Юнусабадский филиал');

    app()->setLocale('uz_Cyrl');
    expect($region->name)->toBe('Юнусобод филиали');
    expect($region->title)->toBe('Юнусобод филиали');

    // Fallback when ru or crl is null/empty
    $fallbackReg = Region::create([
        'name_uz' => 'Faqat uzbekcha filial',
        'name_ru' => null,
        'name_crl' => '',
        'is_active' => true,
    ]);

    app()->setLocale('ru');
    expect($fallbackReg->name)->toBe('Faqat uzbekcha filial');
    expect($fallbackReg->title)->toBe('Faqat uzbekcha filial');

    app()->setLocale('uz_Cyrl');
    expect($fallbackReg->name)->toBe('Faqat uzbekcha filial');
    expect($fallbackReg->title)->toBe('Faqat uzbekcha filial');
});

test('region report title selects localized column at database query level and groups properly', function () {
    $region = Region::create([
        'name_uz' => 'Chilonzor filiali',
        'name_ru' => 'Чиланзарский филиал',
        'name_crl' => 'Чилонзор филиали',
        'is_active' => true,
    ]);

    $category = Category::factory()->create();
    $car = Car::factory()->create();

    Order::factory()->create([
        'car_id' => $car->id,
        'category_id' => $category->id,
        'region_id' => $region->id,
        'operator_id' => $this->admin->id,
        'washer_id' => $this->admin->id,
        'price' => 50000,
        'status' => OrderStatus::DONE->value,
        'finished_at' => now(),
    ]);

    $orderRepo = app(OrderRepositoryInterface::class);
    $filter = new ReportFilterDTO;

    // In Uzbek
    app()->setLocale('uz');
    $reportUz = $orderRepo->reportByRegion($filter);
    expect($reportUz->where('id', $region->id)->first()->title)->toBe('Chilonzor filiali');

    // In Russian
    app()->setLocale('ru');
    $reportRu = $orderRepo->reportByRegion($filter);
    expect($reportRu->where('id', $region->id)->first()->title)->toBe('Чиланзарский филиал');

    // In Uzbek Cyrillic
    app()->setLocale('uz_Cyrl');
    $reportCrl = $orderRepo->reportByRegion($filter);
    expect($reportCrl->where('id', $region->id)->first()->title)->toBe('Чилонзор филиали');
});

test('region can be created and updated with name_uz, name_ru, name_uz_Cyrl via form', function () {
    $this->actingAs($this->admin)->post(route('regions.store'), [
        'name_uz' => 'Shayxontohur filiali',
        'name_ru' => 'Шайхантахурский филиал',
        'name_uz_Cyrl' => 'Шайхонтоҳур филиали',
        'is_active' => 1,
    ])->assertRedirect(route('regions.index'));

    $created = Region::where('name_uz', 'Shayxontohur filiali')->first();
    expect($created)->not->toBeNull();
    expect($created->name_uz)->toBe('Shayxontohur filiali');
    expect($created->name_ru)->toBe('Шайхантахурский филиал');
    expect($created->name_crl)->toBe('Шайхонтоҳур филиали');

    $this->actingAs($this->admin)->put(route('regions.update', $created->id), [
        'name_uz' => 'Shayxontohur filiali yangi',
        'name_ru' => 'Шайхантахурский филиал новый',
        'name_uz_Cyrl' => 'Шайхонтоҳур филиали янги',
        'is_active' => 1,
    ])->assertRedirect(route('regions.index'));

    $updated = $created->fresh();
    expect($updated->name_uz)->toBe('Shayxontohur filiali yangi');
    expect($updated->name_ru)->toBe('Шайхантахурский филиал новый');
    expect($updated->name_crl)->toBe('Шайхонтоҳур филиали янги');
});
