<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name_uz')) {
            $nameCrl = (string) $this->input('name_crl', $this->input('name_uz_Cyrl', ''));
            $this->merge([
                'name_crl' => $nameCrl,
                'name' => [
                    'uz' => (string) $this->input('name_uz'),
                    'uz_Cyrl' => $nameCrl,
                    'ru' => (string) $this->input('name_ru', ''),
                    'en' => (string) $this->input('name_en', ''),
                ],
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'name_uz' => ['nullable', 'string', 'max:255'],
            'name_ru' => ['nullable', 'string', 'max:255'],
            'name_crl' => ['nullable', 'string', 'max:255'],
            'name_uz_Cyrl' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($this->has('name_uz')) {
            $rules['name_uz'] = ['required', 'string', 'max:255'];
        } elseif (is_array($this->input('name'))) {
            $rules['name.uz'] = ['required', 'string', 'max:255'];
            $rules['name.uz_Cyrl'] = ['nullable', 'string', 'max:255'];
            $rules['name.ru'] = ['nullable', 'string', 'max:255'];
            $rules['name.en'] = ['nullable', 'string', 'max:255'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }
}
