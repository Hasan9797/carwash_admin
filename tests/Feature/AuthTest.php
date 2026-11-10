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
