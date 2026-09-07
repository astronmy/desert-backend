<?php

namespace App\Http\Requests\Admin\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canPermission('eventos.contenido') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_mobile_image' => ['nullable', 'boolean'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'max:5120'],
            'delete_gallery' => ['nullable', 'array'],
            'delete_gallery.*' => ['integer', 'exists:event_images,id'],
        ];
    }
}
