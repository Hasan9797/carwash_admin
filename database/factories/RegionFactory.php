<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        $city = fake()->city();

        return [
            'name_uz' => $city.' filiali',
            'name_ru' => $city.' филиал',
            'name_crl' => $city.' филиали',
            'is_active' => true,
        ];
    }
}
