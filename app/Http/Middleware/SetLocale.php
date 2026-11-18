<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var array<int, string>
     */
    public const SUPPORTED_LOCALES = ['uz', 'uz_Cyrl', 'ru'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() && ($request->is('login') || $request->routeIs('login*'))) {
            $locale = 'uz_Cyrl';
        } else {
            $userLocale = $request->user()?->locale;
            $sessionLocale = $request->hasSession() ? $request->session()->get('locale') : null;
            $cookieLocale = $request->cookie('locale');
            $defaultLocale = (string) config('app.locale', 'uz_Cyrl');

            $locale = $userLocale ?: ($sessionLocale ?: ($cookieLocale ?: $defaultLocale));

            if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
                $locale = $defaultLocale;
            }
        }

        App::setLocale($locale);
        Carbon::setLocale(carbon_locale($locale));

        return $next($request);
    }
}
