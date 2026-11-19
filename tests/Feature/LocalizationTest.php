<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Category;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;

beforeEach(function () {
    $this->region = Region::factory()->create(['name' => 'Chilonzor filiali']);
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
        'locale' => 'uz',
    ]);
});

test('locale can be switched via route and persisted in session and cookie', function () {
    $response = $this->get('/locale/ru');

    $response->assertRedirect();
    $response->assertSessionHas('locale', 'ru');
    $response->assertCookie('locale', 'ru');

    $responseUz = $this->get('/locale/uz');
    $responseUz->assertRedirect();
    $responseUz->assertSessionHas('locale', 'uz');
    $responseUz->assertCookie('locale', 'uz');
});

test('authenticated user switching locale updates user model in database', function () {
    $this->actingAs($this->admin)->get('/locale/ru');

    expect($this->admin->fresh()->locale)->toBe('ru');

    $this->actingAs($this->admin)->get('/locale/uz');

    expect($this->admin->fresh()->locale)->toBe('uz');
});

test('invalid or disabled locale returns 404', function () {
    $this->get('/locale/fr')->assertStatus(404);
    $this->get('/locale/en')->assertStatus(404);
});

test('guest sees Cyrillic Uzbek localized texts on login page by default and no language switcher', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('Тизимга кириш');
    $response->assertSee('Carwash бошқарув панели');
    $response->assertSee('Автомойка жараёнларини бошқариш тизими');
    $response->assertSee('Email манзили');
    $response->assertSee('Парол');
    $response->assertSee('Кириш');
    $response->assertDontSee('role="listbox"', false);
});

test('authenticated user sees Russian UI when locale is set to ru', function () {
    $category = Category::factory()->create(['name' => 'Kuzov yuvish']);
    $car = Car::factory()->create(['plate_number' => '01B999BB']);
    Order::factory()->create([
        'car_id' => $car->id,
        'category_id' => $category->id,
        'region_id' => $this->region->id,
        'status' => OrderStatus::IN_PROGRESS,
    ]);

    $this->admin->update(['locale' => 'ru']);

    // Dashboard in Russian
    $dashboard = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/dashboard');

    $dashboard->assertStatus(200);
    $dashboard->assertSee('Панель управления');
    $dashboard->assertSee('Все заказы');
    $dashboard->assertSee('Количество мойщиков');

    // Orders index in Russian
    $orders = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/orders');

    $orders->assertStatus(200);
    $orders->assertSee('Заказы');
    $orders->assertSee('Создать новый заказ');
    $orders->assertSee('В процессе'); // Russian OrderStatus label

    // Users index in Russian
    $users = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/users');

    $users->assertStatus(200);
    $users->assertSee('Сотрудники и пользователи');
    $users->assertSee('Новый пользователь');

    // Categories in Russian
    $categories = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/categories');

    $categories->assertStatus(200);
    $categories->assertSee('Услуги мойки');
    $categories->assertSee('Новый вид услуги');

    // Regions in Russian
    $regions = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/regions');

    $regions->assertStatus(200);
    $regions->assertSee('Список филиалов');
    $regions->assertSee('Новый филиал');

    // Reports in Russian
    $reports = $this->actingAs($this->admin)
        ->withSession(['locale' => 'ru'])
        ->get('/reports/category');

    $reports->assertStatus(200);
    $reports->assertSee('Виды услуг');
    $reports->assertSee('По филиалам');
    $reports->assertSee('По операторам');
    $reports->assertSee('По мойщикам');
});

test('translatable categories and regions support uz and ru and fallback to uz', function () {
    $category = Category::create([
        'name' => [
            'uz' => 'Kimyoviy tozalash',
            'ru' => 'Химчистка салона',
        ],
        'price' => 150000,
        'is_active' => true,
    ]);

    app()->setLocale('uz');
    expect($category->name)->toBe('Kimyoviy tozalash');

    app()->setLocale('ru');
    expect($category->name)->toBe('Химчистка салона');

    // Fallback test
    $categoryOnlyUz = Category::create([
        'name' => [
            'uz' => 'Faqat uzbekcha xizmat',
        ],
        'price' => 50000,
        'is_active' => true,
    ]);

    app()->setLocale('ru');
    expect($categoryOnlyUz->name)->toBe('Faqat uzbekcha xizmat');
});

test('category and region admin forms support creating with uz and ru inputs', function () {
    $this->actingAs($this->admin)->post(route('categories.store'), [
        'name_uz' => 'VIP yuvish',
        'name_ru' => 'VIP мойка',
        'description_uz' => 'To\'liq tozalash',
        'description_ru' => 'Полная чистка',
        'price' => 200000,
        'is_active' => 1,
    ])->assertRedirect(route('categories.index'));

    $createdCategory = Category::where('price', 200000)->first();
    expect($createdCategory)->not->toBeNull();
    expect($createdCategory->getTranslation('name', 'uz'))->toBe('VIP yuvish');
    expect($createdCategory->getTranslation('name', 'ru'))->toBe('VIP мойка');

    $this->actingAs($this->admin)->post(route('regions.store'), [
        'name_uz' => 'Yunusobod filiali',
        'name_ru' => 'Юнусабадский филиал',
        'is_active' => 1,
    ])->assertRedirect(route('regions.index'));

    $createdRegion = Region::where('name_uz', 'Yunusobod filiali')->first();
    expect($createdRegion)->not->toBeNull();
    expect($createdRegion->getTranslation('name', 'uz'))->toBe('Yunusobod filiali');
    expect($createdRegion->getTranslation('name', 'ru'))->toBe('Юнусабадский филиал');
});

test('locale can be switched to uz_Cyrl and user sees Uzbek Cyrillic UI', function () {
    $responseCyrl = $this->get('/locale/uz_Cyrl');
    $responseCyrl->assertRedirect();
    $responseCyrl->assertSessionHas('locale', 'uz_Cyrl');
    $responseCyrl->assertCookie('locale', 'uz_Cyrl');

    $this->admin->update(['locale' => 'uz_Cyrl']);

    $category = Category::create([
        'name' => [
            'uz' => 'Standart yuvish',
            'uz_Cyrl' => 'Стандарт ювиш',
        ],
        'price' => 50000,
        'is_active' => true,
    ]);
    $car = Car::factory()->create(['plate_number' => '01C111CC']);
    Order::factory()->create([
        'car_id' => $car->id,
        'category_id' => $category->id,
        'region_id' => $this->region->id,
        'status' => OrderStatus::IN_PROGRESS,
    ]);

    // Dashboard in uz_Cyrl
    $dashboard = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/dashboard');

    $dashboard->assertStatus(200);
    $dashboard->assertSee('Бошқарув панели');
    $dashboard->assertSee('Барча заказлар');
    $dashboard->assertSee('Ювувчилар сони');

    // Orders index in uz_Cyrl
    $orders = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/orders');

    $orders->assertStatus(200);
    $orders->assertSee('Заказлар');
    $orders->assertSee('Янги заказ очиш');
    $orders->assertSee('Жараёнда');
    $orders->assertSee('Стандарт ювиш');
    $orders->assertSee('сўм');

    // Users index in uz_Cyrl
    $users = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/users');

    $users->assertStatus(200);
    $users->assertSee('Ходимлар ва фойдаланувчилар');
    $users->assertSee('Янги фойдаланувчи');

    // Categories in uz_Cyrl
    $categories = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/categories');

    $categories->assertStatus(200);
    $categories->assertSee('Ювиш хизматлари');
    $categories->assertSee('Янги хизмат тури');

    // Regions in uz_Cyrl
    $regions = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/regions');

    $regions->assertStatus(200);
    $regions->assertSee('Филиаллар рўйхати');
    $regions->assertSee('Янги филиал');

    // Reports in uz_Cyrl
    $reports = $this->actingAs($this->admin)
        ->withSession(['locale' => 'uz_Cyrl'])
        ->get('/reports/category');

    $reports->assertStatus(200);
    $reports->assertSee('Ҳисоботлар');
    $reports->assertSee('Хизмат турлари');
    $reports->assertSee('Филиаллар бўйича');
});

test('carbon_locale and html_lang helpers work correctly across locales', function () {
    expect(carbon_locale('uz'))->toBe('uz_Latn');
    expect(carbon_locale('uz_Cyrl'))->toBe('uz_Cyrl');
    expect(carbon_locale('ru'))->toBe('ru');
    expect(carbon_locale('en'))->toBe('en');

    expect(html_lang('uz'))->toBe('uz-Latn');
    expect(html_lang('uz_Cyrl'))->toBe('uz-Cyrl');
    expect(html_lang('ru'))->toBe('ru');
    expect(html_lang('en'))->toBe('en');
});
