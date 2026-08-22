<?php

namespace App\Http\Requests\Admin\User;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canPermission('usuarios.editar') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $roleId = $this->input('role_id');
            if (! $roleId) {
                return;
            }

            $role = Role::query()->find($roleId);
            if (! $role) {
                return;
            }

            if (! $role->is_active) {
                $validator->errors()->add('role_id', __('user.validation.role_inactive'));
            }

            if ($role->requires_event && blank($this->input('event_id'))) {
                $validator->errors()->add('event_id', __('user.validation.event_required'));
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if (is_array($data)) {
            $role = Role::query()->find($data['role_id'] ?? null);
            if ($role && ! $role->requires_event) {
                $data['event_id'] = null;
            }
        }

        return $data;
    }
}
