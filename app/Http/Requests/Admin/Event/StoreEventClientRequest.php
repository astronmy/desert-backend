<?php

namespace App\Http\Requests\Admin\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreEventClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canPermission('usuarios.crear') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('password') === '' || $this->input('password') === null) {
            $this->merge(['password' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', Password::defaults()],
        ];
    }
}
