<?php

namespace App\Http\Requests\Api;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'max:50'],
            'id_type' => ['required', Rule::enum(DocumentType::class)],
            'selfie' => ['required', 'image', 'max:5120'],
            'uuid_notification' => ['nullable', 'string', 'max:255'],
        ];
    }
}
