<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: int
{
    case NEW = 1;
    case IN_PROGRESS = 2;
    case DONE = 3;
    case CANCELLED = 4;

    public function label(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en' => match ($this) {
                self::NEW => 'New',
                self::IN_PROGRESS => 'In Progress',
                self::DONE => 'Done',
                self::CANCELLED => 'Cancelled',
            },
            'ru' => match ($this) {
                self::NEW => 'Новый',
                self::IN_PROGRESS => 'В процессе',
                self::DONE => 'Завершен',
                self::CANCELLED => 'Отменен',
            },
            default => match ($this) {
                self::NEW => 'Yangi',
                self::IN_PROGRESS => 'Jarayonda',
                self::DONE => 'Bajarildi',
                self::CANCELLED => 'Bekor qilindi',
            },
        };
    }

    public static function labelFor(int|self $status, ?string $locale = null): string
    {
        $enum = $status instanceof self ? $status : (self::tryFrom($status) ?? self::NEW);

        return $enum->label($locale);
    }

    public static function getLabel(int|self $status, ?string $locale = null): string
    {
        return self::labelFor($status, $locale);
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
            self::NEW => 'new',
            self::IN_PROGRESS => 'in_progress',
            self::DONE => 'done',
            self::CANCELLED => 'cancelled',
        };
    }

    public static function fromName(string $name): ?self
    {
        return match (strtolower(trim($name))) {
            'new' => self::NEW,
            'in_progress' => self::IN_PROGRESS,
            'done' => self::DONE,
            'cancelled' => self::CANCELLED,
            default => null,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW => 'bg-sky-100 text-sky-800 border-sky-200',
            self::IN_PROGRESS => 'bg-amber-100 text-amber-800 border-amber-200',
            self::DONE => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::NEW => in_array($target, [self::IN_PROGRESS, self::CANCELLED], true),
            self::IN_PROGRESS => in_array($target, [self::DONE, self::CANCELLED], true),
            self::DONE, self::CANCELLED => false,
        };
    }
}
