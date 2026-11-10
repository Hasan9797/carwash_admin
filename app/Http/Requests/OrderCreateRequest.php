<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\OrderCreateDTO;
use App\Models\Car;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate_mode' => ['required', 'string', 'in:camera,manual'],
            'car_id' => ['nullable', 'required_if:plate_mode,camera', 'exists:cars,id'],
            'plate_number' => ['nullable', 'required_if:plate_mode,manual', 'string', 'max:32'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'washer_id' => ['required', 'integer', 'exists:users,id'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $mode = $this->input('plate_mode');
            if ($mode === 'camera' && empty($this->input('car_id'))) {
                $validator->errors()->add('car_id', 'Kameradan o\'tgan mashinani tanlang.');
            }

            if ($mode === 'manual' && empty(trim((string) $this->input('plate_number')))) {
                $validator->errors()->add('plate_number', 'Avtomobil davlat raqamini qo\'lda kiriting.');
            }
        });
    }

    public function toDto(): OrderCreateDTO
    {
        $mode = $this->input('plate_mode');
        $carId = null;
        $plateNumber = '';

        if ($mode === 'camera') {
            $car = Car::find($this->input('car_id'));
            $carId = $car?->id;
            $plateNumber = (string) $car?->plate_number;
        } else {
            $plateNumber = (string) $this->input('plate_number');
        }

        // Region operatorning regionidan olinadi yoki requestdan
        $user = $this->user();
        $regionId = (int) ($this->input('region_id') ?? $user?->region_id ?? 1);

        return new OrderCreateDTO(
            plateNumber: $plateNumber,
            categoryId: (int) $this->input('category_id'),
            operatorId: (int) $user->id,
            washerId: (int) $this->input('washer_id'),
            regionId: $regionId,
            carId: $carId,
        );
    }
}
