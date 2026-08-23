<?php

namespace App\Http\Requests\Api;

use App\Models\EventNotification;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $notification = $this->route('event_notification');

        return $user !== null
            && $notification instanceof EventNotification
            && $user->canAccessEvent($notification->event_id);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ];
    }
}
