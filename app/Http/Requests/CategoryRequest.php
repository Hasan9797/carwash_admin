<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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

        if ($this->has('description_uz')) {
            $this->merge([
                'description' => [
                    'uz' => (string) $this->input('description_uz', ''),
                    'uz_Cyrl' => (string) $this->input('description_uz_Cyrl', ''),
                    'ru' => (string) $this->input('description_ru', ''),
                    'en' => (string) $this->input('description_en', ''),
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
            'price' => ['required', 'numeric', 'min:0'],
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

        if (is_array($this->input('description'))) {
            $rules['description.uz'] = ['nullable', 'string'];
            $rules['description.uz_Cyrl'] = ['nullable', 'string'];
            $rules['description.ru'] = ['nullable', 'string'];
            $rules['description.en'] = ['nullable', 'string'];
        } else {
            $rules['description'] = ['nullable', 'string'];
        }

        return $rules;
    }
}
