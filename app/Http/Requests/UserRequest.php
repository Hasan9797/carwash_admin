<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();
        $targetUserId = $this->route('user');

        if ($actor?->isOperator() && $targetUserId) {
            $targetUser = \App\Models\User::find($targetUserId);
            if ($targetUser && $targetUser->isAdmin()) {
                return false;
            }
        }

        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? (int) $this->route('user') : null;
        $actor = $this->user();

        $roleRules = ['required', Rule::enum(UserRole::class)];
        if ($actor?->isOperator()) {
            $roleRules[] = Rule::notIn([UserRole::ADMIN->value]);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => $roleRules,
            'phone' => ['nullable', 'string', 'max:30'],
            'chat_id' => ['nullable', 'string', 'max:64', Rule::unique('users', 'chat_id')->ignore($userId)],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
