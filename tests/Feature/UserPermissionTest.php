<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Region;
use App\Models\User;

beforeEach(function () {
    $this->region = Region::create([
        'name_uz' => 'Chilonzor Filial',
        'is_active' => true,
    ]);

    $this->admin = User::create([
        'name' => 'Bosh Admin',
        'email' => 'admin_perm@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::ADMIN,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->operator = User::create([
        'name' => 'Operator Bek',
        'email' => 'operator_perm@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::OPERATOR,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);

    $this->washer = User::create([
        'name' => 'Yuvuvchi Jamshid',
        'email' => 'washer_perm@example.com',
        'password' => bcrypt('password'),
        'role' => UserRole::WASHER,
        'region_id' => $this->region->id,
        'is_active' => true,
    ]);
});

test('operator can view users list but admin users are excluded', function () {
    $response = $this->actingAs($this->operator)->get(route('users.index'));

    $response->assertOk()
        ->assertSee('Operator Bek')
        ->assertSee('Yuvuvchi Jamshid')
        ->assertDontSee('Bosh Admin');
});

test('admin can see all users in users list including other admins', function () {
    $response = $this->actingAs($this->admin)->get(route('users.index'));

    $response->assertOk()
        ->assertSee('Bosh Admin')
        ->assertSee('Operator Bek')
        ->assertSee('Yuvuvchi Jamshid');
});

test('operator receives 403 when trying to edit an admin user', function () {
    $response = $this->actingAs($this->operator)->get(route('users.edit', $this->admin->id));

    $response->assertForbidden();
});

test('operator receives 403 when trying to update an admin user', function () {
    $response = $this->actingAs($this->operator)->put(route('users.update', $this->admin->id), [
        'name' => 'Hacked Admin',
        'email' => 'admin_perm@example.com',
        'role' => UserRole::ADMIN->value,
    ]);

    $response->assertForbidden();
});

test('operator receives 403 when trying to delete an admin user', function () {
    $response = $this->actingAs($this->operator)->delete(route('users.destroy', $this->admin->id));

    $response->assertForbidden();
});

test('operator cannot create a user with admin role', function () {
    $response = $this->actingAs($this->operator)->post(route('users.store'), [
        'name' => 'Yangi Admin',
        'email' => 'newadmin@example.com',
        'role' => UserRole::ADMIN->value,
        'password' => 'secret123',
    ]);

    $response->assertSessionHasErrors(['role']);
    $this->assertDatabaseMissing('users', ['email' => 'newadmin@example.com']);
});

test('operator can create, edit and delete a washer or operator', function () {
    // 1. Create washer
    $createResponse = $this->actingAs($this->operator)->post(route('users.store'), [
        'name' => 'Yangi Yuvuvchi',
        'email' => 'newwasher@example.com',
        'role' => UserRole::WASHER->value,
        'phone' => '+998901112233',
        'chat_id' => '99887766',
        'region_id' => $this->region->id,
        'password' => 'secret123',
    ]);

    $createResponse->assertRedirect(route('users.index'));
    $createdUser = User::where('email', 'newwasher@example.com')->first();
    expect($createdUser)->not->toBeNull()
        ->and($createdUser->role)->toBe(UserRole::WASHER)
        ->and($createdUser->chat_id)->toBe('99887766');

    // 2. Edit washer
    $editResponse = $this->actingAs($this->operator)->get(route('users.edit', $createdUser->id));
    $editResponse->assertOk()
        ->assertSee('Yangi Yuvuvchi');

    // 3. Update washer
    $updateResponse = $this->actingAs($this->operator)->put(route('users.update', $createdUser->id), [
        'name' => 'Yangi Yuvuvchi Yangilandi',
        'email' => 'newwasher@example.com',
        'role' => UserRole::WASHER->value,
        'phone' => '+998901112233',
        'chat_id' => '99887766',
        'region_id' => $this->region->id,
    ]);

    $updateResponse->assertRedirect(route('users.index'));
    expect($createdUser->fresh()->name)->toBe('Yangi Yuvuvchi Yangilandi');

    // 4. Delete washer
    $deleteResponse = $this->actingAs($this->operator)->delete(route('users.destroy', $createdUser->id));
    $deleteResponse->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);
});
