<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 1;
    case OPERATOR = 2;
    case WASHER = 3;

    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en' => match ($this) {
                self::ADMIN => 'Admin',
                self::OPERATOR => 'Operator',
                self::WASHER => 'Washer',
            },
            'ru' => match ($this) {
                self::ADMIN => 'Администратор',
                self::OPERATOR => 'Оператор',
                self::WASHER => 'Мойщик',
            },
            default => match ($this) {
                self::ADMIN => 'Admin',
                self::OPERATOR => 'Operator',
                self::WASHER => 'Yuvuvchi',
            },
        };
    }

    public static function labelFor(int|self $role, ?string $locale = null): string
    {
        $enum = $role instanceof self ? $role : (self::tryFrom($role) ?? self::OPERATOR);

        return $enum->label($locale);
    }

    public static function getLabel(int|self $role, ?string $locale = null): string
    {
        return self::labelFor($role, $locale);
    }

    /**
     * @return array<int, string>
     */
    public static function labels(?string $locale = null): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label($locale);
        }

        return $result;
    }

    public function nameSlug(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::OPERATOR => 'operator',
            self::WASHER => 'washer',
        };
    }

    public static function fromName(string $name): ?self
    {
        return match (strtolower(trim($name))) {
            'admin' => self::ADMIN,
            'operator' => self::OPERATOR,
            'washer' => self::WASHER,
            default => null,
        };
    }

    public function canLogin(): bool
    {
        return in_array($this, [self::ADMIN, self::OPERATOR], true);
    }
}
