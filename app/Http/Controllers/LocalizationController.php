<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetLocale::SUPPORTED_LOCALES, true)) {
            abort(404);
        }

        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
    }
}
