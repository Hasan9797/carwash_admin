<?php

declare(strict_types=1);

use Carbon\Carbon;

if (! function_exists('carbon_locale')) {
    /**
     * Map application locale to Carbon-compatible locale identifier.
     */
    function carbon_locale(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return match ($locale) {
            'uz' => 'uz_Latn',
            'uz_Cyrl' => 'uz_Cyrl',
            'ru' => 'ru',
            'en' => 'en',
            default => 'uz_Latn',
        };
    }
}

if (! function_exists('html_lang')) {
    /**
     * Get HTML lang attribute code according to W3C / BCP 47 specs.
     */
    function html_lang(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return match ($locale) {
            'uz_Cyrl' => 'uz-Cyrl',
            'uz' => 'uz-Latn',
            'ru' => 'ru',
            'en' => 'en',
            default => str_replace('_', '-', $locale),
        };
    }
}

if (! function_exists('supported_locales')) {
    /**
     * Get list of supported locales with codes, short labels, and native names.
     *
     * @return array<string, array{code: string, short: string, name: string}>
     */
    function supported_locales(): array
    {
        $registry = [
            'uz' => [
                'code' => 'uz',
                'short' => 'UZ',
                'name' => "O'zbekcha",
            ],
            'uz_Cyrl' => [
                'code' => 'uz_Cyrl',
                'short' => 'ЎЗ',
                'name' => 'Ўзбекча',
            ],
            'ru' => [
                'code' => 'ru',
                'short' => 'RU',
                'name' => 'Русский',
            ],
            'en' => [
                'code' => 'en',
                'short' => 'EN',
                'name' => 'English',
            ],
        ];

        $available = (array) config('app.available_locales', array_keys($registry));

        $result = [];
        foreach ($available as $code) {
            if (isset($registry[$code])) {
                $result[$code] = $registry[$code];
            } else {
                $result[$code] = [
                    'code' => $code,
                    'short' => strtoupper(substr($code, 0, 2)),
                    'name' => strtoupper($code),
                ];
            }
        }

        return $result;
    }
}

if (! function_exists('current_locale_meta')) {
    /**
     * Get metadata of the currently active locale.
     *
     * @return array{code: string, short: string, name: string}
     */
    function current_locale_meta(): array
    {
        $locales = supported_locales();
        $current = app()->getLocale();

        return $locales[$current] ?? [
            'code' => $current,
            'short' => strtoupper(substr($current, 0, 2)),
            'name' => strtoupper($current),
        ];
    }
}

if (! function_exists('format_money')) {
    /**
     * Format money with space thousands separator and localized currency suffix.
     */
    function format_money(int|float|string|null $amount, bool $withCurrency = true): string
    {
        $formatted = number_format((float) ($amount ?? 0), 0, '.', ' ');

        return $withCurrency ? $formatted.' '.__('common.sum') : $formatted;
    }
}

if (! function_exists('format_date')) {
    /**
     * Format a date using Carbon localized format.
     */
    function format_date(mixed $date, string $format = 'D MMM, YYYY'): string
    {
        if (! $date) {
            return '—';
        }

        return Carbon::parse($date)->locale(carbon_locale())->isoFormat($format);
    }
}

if (! function_exists('format_datetime')) {
    /**
     * Format a datetime using Carbon localized format.
     */
    function format_datetime(mixed $date, string $format = 'D MMM, YYYY HH:mm'): string
    {
        if (! $date) {
            return '—';
        }

        return Carbon::parse($date)->locale(carbon_locale())->isoFormat($format);
    }
}
