<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvitationFirebaseTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $platform = $this->header('device-platform');
        $version = $this->header('device-app-version');

        $this->merge([
            'device_platform' => is_string($platform) ? strtolower(trim($platform)) : null,
            'device_app_version' => is_string($version) ? trim($version) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'firebase_token' => ['required', 'string', 'max:4096'],
            'device_platform' => ['required', Rule::in(['ios', 'android'])],
            'device_app_version' => ['nullable', 'string', 'max:255'],
        ];
    }
}
