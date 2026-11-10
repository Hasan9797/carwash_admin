<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'camera_id',
        'image_path',
        'detected_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    protected function plateNumber(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? self::normalizePlateNumber($value) : null,
        );
    }

    public static function normalizePlateNumber(string $plate): string
    {
        // Katta harf va bo'shliqlarsiz
        return strtoupper((string) preg_replace('/\s+/', '', trim($plate)));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }
}
