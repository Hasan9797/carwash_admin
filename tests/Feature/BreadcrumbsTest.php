<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Car;
use App\Models\Category;
use App\Models\Region;
use App\Models\User;

beforeEach(function () {
    $this->region = Region::factory()->create(['name' => 'Chilonzor filiali']);
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
    ]);
});

test('dashboard does not display breadcrumbs', function () {
    $response = $this->actingAs($this->admin)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertDontSee('aria-label="Breadcrumb"', false);
    $response->assertSee('<title>Boshqaruv paneli - ', false);
});

test('breadcrumbs render correctly across all inner application pages', function () {
    $car = Car::factory()->create([
        'plate_number' => '01A777AA',
    ]);
    $category = Category::factory()->create(['name' => 'Standart yuvish']);
    $user = User::factory()->create([
        'name' => 'Sardor Rahim',
        'role' => UserRole::WASHER,
        'region_id' => $this->region->id,
    ]);

    $pages = [
        ['url' => '/orders', 'crumb' => 'Zakazlar', 'title' => 'Zakazlar'],
        ['url' => '/orders/create', 'crumb' => 'Yangi zakaz', 'title' => 'Yangi zakaz'],
        ['url' => '/cars', 'crumb' => 'Kameradagi mashinalar', 'title' => 'Kameradagi mashinalar'],
        ['url' => "/cars/{$car->id}", 'crumb' => '01A777AA', 'title' => '01A777AA'],
        ['url' => '/reports/category', 'crumb' => 'Xizmat turlari', 'title' => 'Xizmat turlari'],
        ['url' => '/reports/region', 'crumb' => 'Filiallar bo\'yicha', 'title' => 'Filiallar bo\'yicha'],
        ['url' => '/reports/operator', 'crumb' => 'Operatorlar bo\'yicha', 'title' => 'Operatorlar bo\'yicha'],
        ['url' => '/reports/washer', 'crumb' => 'Yuvuvchilar bo\'yicha', 'title' => 'Yuvuvchilar bo\'yicha'],
        ['url' => '/users', 'crumb' => 'Foydalanuvchilar', 'title' => 'Foydalanuvchilar'],
        ['url' => '/users/create', 'crumb' => 'Yangi foydalanuvchi', 'title' => 'Yangi foydalanuvchi'],
        ['url' => "/users/{$user->id}/edit", 'crumb' => 'Tahrirlash: Sardor Rahim', 'title' => 'Tahrirlash: Sardor Rahim'],
        ['url' => '/categories', 'crumb' => 'Xizmat turlari', 'title' => 'Xizmat turlari'],
        ['url' => '/categories/create', 'crumb' => 'Yangi xizmat turi', 'title' => 'Yangi xizmat turi'],
        ['url' => "/categories/{$category->id}/edit", 'crumb' => 'Tahrirlash: Standart yuvish', 'title' => 'Tahrirlash: Standart yuvish'],
        ['url' => '/regions', 'crumb' => 'Filiallar', 'title' => 'Filiallar'],
        ['url' => '/regions/create', 'crumb' => 'Yangi filial', 'title' => 'Yangi filial'],
        ['url' => "/regions/{$this->region->id}/edit", 'crumb' => 'Tahrirlash: Chilonzor filiali', 'title' => 'Tahrirlash: Chilonzor filiali'],
    ];

    foreach ($pages as $page) {
        $response = $this->actingAs($this->admin)->get($page['url']);
        $response->assertStatus(200);
        $response->assertSee('aria-label="Breadcrumb"', false);
        $response->assertSee($page['crumb']);
        $escapedTitle = e($page['title']);
        $response->assertSee("<title>{$escapedTitle} - ", false);
    }
});
