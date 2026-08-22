<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvitationsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly Event $event) {}

    public function collection(): Collection
    {
        return Invitation::query()
            ->with('guest')
            ->where('event_id', $this->event->id)
            ->orderBy('id')
            ->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            'Apellido',
            'Tipo documento',
            'Documento',
            'Estado',
            'Confirmado en',
            'Creado en',
        ];
    }

    /**
     * @param  Invitation  $invitation
     * @return list<string|null>
     */
    public function map($invitation): array
    {
        $guest = $invitation->guest;

        return [
            $invitation->code,
            $guest?->first_name,
            $guest?->last_name,
            $guest?->id_type?->value ?? $guest?->id_type,
            $guest?->document_number,
            $invitation->status instanceof \BackedEnum ? $invitation->status->value : (string) $invitation->status,
            $invitation->confirmed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            $invitation->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
        ];
    }
}
