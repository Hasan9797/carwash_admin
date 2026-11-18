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
            $this->merge([
                'name' => [
                    'uz' => (string) $this->input('name_uz'),
                    'uz_Cyrl' => (string) $this->input('name_uz_Cyrl', ''),
                    'ru' => (string) $this->input('name_ru', ''),
                    'en' => (string) $this->input('name_en', ''),
                ],
            ]);
        }
    }

    public function rules(): array
    {
        $rules = [
            'is_active' => ['nullable', 'boolean'],
        ];

        if (is_array($this->input('name'))) {
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
