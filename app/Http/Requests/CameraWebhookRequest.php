<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CameraWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate_number' => ['nullable', 'string', 'max:32'],
            'plate' => ['nullable', 'string', 'max:32'],
            'license_plate' => ['nullable', 'string', 'max:32'],
            'camera_id' => ['nullable', 'string', 'max:64'],
            'image' => ['nullable', 'string'],
            'detected_at' => ['nullable'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $hasPlate = $this->filled('plate_number')
                || $this->filled('plate')
                || $this->filled('license_plate');

            if (! $hasPlate) {
                $validator->errors()->add('plate_number', 'Avtomobil davlat raqami majburiy (plate_number yoki plate).');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validatsiya xatosi.',
            'errors' => $validator->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
