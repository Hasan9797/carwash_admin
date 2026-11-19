<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_uz',
        'name_ru',
        'name_crl',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return match ($locale) {
            'ru' => $this->name_ru ?: ($this->name_uz ?? ''),
            'uz_Cyrl', 'crl' => $this->name_crl ?: ($this->name_uz ?? ''),
            default => $this->name_uz ?? '',
        };
    }

    public function getTitleAttribute(): string
    {
        return $this->getNameAttribute();
    }

    public function setNameAttribute(mixed $value): void
    {
        if (is_array($value)) {
            if (isset($value['uz'])) {
                $this->attributes['name_uz'] = (string) $value['uz'];
            }
            if (isset($value['ru'])) {
                $this->attributes['name_ru'] = (string) $value['ru'];
            }
            if (isset($value['uz_Cyrl']) || isset($value['crl'])) {
                $this->attributes['name_crl'] = (string) ($value['uz_Cyrl'] ?? $value['crl']);
            }

            return;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $this->setNameAttribute($decoded);

                return;
            }

            $this->attributes['name_uz'] = $value;
        }
    }

    public function getTranslation(string $attribute, string $locale, bool $useFallbackLocale = true): string
    {
        if ($attribute === 'name') {
            return match ($locale) {
                'ru' => $this->name_ru ?: ($useFallbackLocale ? ($this->name_uz ?? '') : ''),
                'uz_Cyrl', 'crl' => $this->name_crl ?: ($useFallbackLocale ? ($this->name_uz ?? '') : ''),
                default => $this->name_uz ?? '',
            };
        }

        return '';
    }

    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        if ($attribute === 'name') {
            match ($locale) {
                'ru' => $this->name_ru = $value,
                'uz_Cyrl', 'crl' => $this->name_crl = $value,
                default => $this->name_uz = $value,
            };
        }

        return $this;
    }

    public function getFallbackLocale(): string
    {
        return 'uz';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
