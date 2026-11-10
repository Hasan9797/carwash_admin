<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ];
    }

    public function getStatus(): OrderStatus
    {
        $status = $this->input('status');

        if ($status instanceof OrderStatus) {
            return $status;
        }

        if (is_numeric($status)) {
            return OrderStatus::from((int) $status);
        }

        return OrderStatus::fromName((string) $status) ?? OrderStatus::NEW;
    }
}
