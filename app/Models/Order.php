<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'plate_number',
        'category_id',
        'operator_id',
        'washer_id',
        'washer_name',
        'washer_phone',
        'region_id',
        'price',
        'status',
        'started_at',
        'finished_at',
    ];

    public function getWasherDisplayNameAttribute(): string
    {
        return $this->washer?->name ?? $this->washer_name ?? '—';
    }

    public function getWasherDisplayPhoneAttribute(): ?string
    {
        return $this->washer?->phone ?? $this->washer_phone ?? null;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => OrderStatus::class,
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected function plateNumber(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value !== null ? Car::normalizePlateNumber($value) : null,
        );
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function washer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'washer_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
