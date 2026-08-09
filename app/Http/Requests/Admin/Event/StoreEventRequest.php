<?php

namespace App\Http\Requests\Admin\Event;

use App\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
        ];
    }
}
