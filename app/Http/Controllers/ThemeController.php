<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(array_keys(config('themes.available', [])))],
        ]);

        $theme = $validated['theme'];

        if ($user = $request->user()) {
            $user->update(['theme' => $theme]);
        }

        return response()->json([
            'success' => true,
            'theme' => $theme,
        ]);
    }
}
