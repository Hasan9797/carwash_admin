<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_theme(): void
    {
        $response = $this->postJson('/theme', ['theme' => 'dark']);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_theme(): void
    {
        $user = User::factory()->create(['theme' => 'light']);

        $response = $this->actingAs($user)->postJson('/theme', [
            'theme' => 'cream',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'theme' => 'cream',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme' => 'cream',
        ]);
    }

    public function test_invalid_theme_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/theme', [
            'theme' => 'invalid_theme',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['theme']);
    }

    public function test_all_configured_themes_have_translations_in_all_locales(): void
    {
        $locales = ['uz', 'uz_Cyrl', 'ru', 'en'];
        $themes = config('themes.available', []);

        $this->assertCount(4, $themes);
        $this->assertArrayHasKey('light', $themes);
        $this->assertArrayHasKey('dark', $themes);
        $this->assertArrayHasKey('cream', $themes);
        $this->assertArrayHasKey('sky', $themes);

        foreach ($locales as $locale) {
            app()->setLocale($locale);
            $this->assertNotEmpty(__('theme.choose'));

            foreach ($themes as $key => $themeData) {
                $translated = __($themeData['name']);
                $this->assertNotEmpty($translated);
                $this->assertNotEquals($themeData['name'], $translated);
            }
        }
    }
}
