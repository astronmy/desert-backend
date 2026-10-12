<?php

namespace App\Http\Requests\Api;

use App\Enums\NotificationScope;
use App\Enums\NotificationType;
use App\Models\Event;
use App\Models\Invitation;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEventNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $eventId = (int) $this->input('event_id');

        return $user !== null && $eventId > 0 && $user->canAccessEvent($eventId);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(NotificationType::class)],
            'scope' => ['required', Rule::enum(NotificationScope::class)],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'send_at' => [
                'required_if:type,'.NotificationType::Scheduled->value,
                'prohibited_if:type,'.NotificationType::Instant->value,
                'nullable',
                'date',
            ],
            'invitation_ids' => [
                'required_if:scope,'.NotificationScope::Specific->value,
                'prohibited_if:scope,'.NotificationScope::General->value,
                'array',
                'min:1',
            ],
            'invitation_ids.*' => ['integer', 'exists:invitations,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateSendAt($validator);

            if ($this->input('scope') !== NotificationScope::Specific->value) {
                return;
            }

            $ids = array_values(array_unique(array_map('intval', $this->input('invitation_ids', []))));
            if ($ids === []) {
                return;
            }

            $validCount = Invitation::query()
                ->where('event_id', (int) $this->input('event_id'))
                ->whereNotNull('firebase_token')
                ->where('firebase_token', '!=', '')
                ->whereIn('id', $ids)
                ->count();

            if ($validCount !== count($ids)) {
                $validator->errors()->add(
                    'invitation_ids',
                    'Solo se pueden seleccionar invitaciones del evento con token de Firebase.'
                );
            }
        });
    }

    private function validateSendAt(Validator $validator): void
    {
        if ($this->input('type') !== NotificationType::Scheduled->value || ! filled($this->input('send_at'))) {
            return;
        }

        try {
            $sendAt = Carbon::parse($this->input('send_at'), Event::REGISTRATION_TIMEZONE);
        } catch (InvalidFormatException) {
            return;
        }

        if (! $sendAt->isFuture()) {
            $validator->errors()->add(
                'send_at',
                'El campo enviar el debe ser una fecha posterior a ahora.'
            );
        }
    }
}
