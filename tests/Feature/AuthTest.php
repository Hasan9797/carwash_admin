<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('operator and admin can login successfully', function () {
    $operator = User::factory()->operator()->create([
        'email' => 'op@example.com',
        'password' => Hash::make('secret123'),
        'is_active' => true,
    ]);

    $response = $this->post('/login', [
        'email' => 'op@example.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($operator);
});

test('login fails with invalid credentials', function () {
    User::factory()->admin()->create([
        'email' => 'adm@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'adm@example.com',
        'password' => 'wrong-pass',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('washer cannot login to the dashboard', function () {
    $washer = User::factory()->washer()->create([
        'email' => 'washer@example.com',
        'password' => Hash::make('secret123'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'washer@example.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('operator cannot access admin-only users crud', function () {
    $operator = User::factory()->operator()->create();

    $response = $this->actingAs($operator)->get('/users');

    $response->assertStatus(403);
});

test('admin can view users index page with breadcrumbs and title', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/users');

    $response->assertStatus(200)
        ->assertSee('Dashboard')
        ->assertSee("Tizim ma'muriyati")
        ->assertSee('Foydalanuvchilar')
        ->assertSee('Xodimlar va foydalanuvchilar')
        ->assertSee('<title>Foydalanuvchilar - ', false);
});

test('login page does not display test credentials or sinov hisoblari', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertDontSee('Sinov hisoblari')
        ->assertDontSee('admin@example.com')
        ->assertDontSee('operator1@example.com');
});
