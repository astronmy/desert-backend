<?php

namespace App\Http\Requests\Admin\Event;

use App\Enums\EventPlace;
use App\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'init_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:init_date'],
            'type' => ['required', Rule::enum(EventType::class)],
            'place' => ['required', Rule::enum(EventPlace::class)],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'host' => ['nullable', 'string', 'max:255'],
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
