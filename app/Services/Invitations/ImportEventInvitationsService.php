<?php

namespace App\Services\Invitations;

use App\Enums\DocumentType;
use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportEventInvitationsService
{
    public function __construct(
        protected InvitationCodeGenerator $codeGenerator
    ) {}

    /**
     * @return array{created: int, reused: int, skipped: int, errors: int}
     */
    public function import(Event $event, UploadedFile $file): array
    {
        /** @var Collection<int, Collection<int, Collection<int|string, mixed>>> $sheets */
        $sheets = Excel::toCollection(null, $file);
        $rows = $sheets->first() ?? collect();

        $summary = [
            'created' => 0,
            'reused' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        if ($rows->isEmpty()) {
            return $summary;
        }

        $headerMap = $this->detectHeaderMap($rows->first());
        $dataRows = $headerMap === null ? $rows : $rows->slice(1);

        DB::transaction(function () use ($event, $dataRows, $headerMap, &$summary) {
            foreach ($dataRows as $row) {
                $mapped = $this->mapRow(collect($row), $headerMap);

                if ($mapped === null
                    || $mapped['document_number'] === ''
                    || $mapped['first_name'] === ''
                    || $mapped['last_name'] === ''
                ) {
                    $summary['errors']++;

                    continue;
                }

                $existingGuest = Guest::query()->where([
                    'id_type' => DocumentType::Dni->value,
                    'document_number' => $mapped['document_number'],
                ])->first();

                if ($existingGuest) {
                    $existingGuest->update([
                        'first_name' => $mapped['first_name'],
                        'last_name' => $mapped['last_name'],
                    ]);
                    $guest = $existingGuest;
                    $summary['reused']++;
                } else {
                    $guest = Guest::create([
                        'first_name' => $mapped['first_name'],
                        'last_name' => $mapped['last_name'],
                        'document_number' => $mapped['document_number'],
                        'id_type' => DocumentType::Dni,
                    ]);
                }

                $exists = Invitation::query()
                    ->where('event_id', $event->id)
                    ->where('guest_id', $guest->id)
                    ->exists();

                if ($exists) {
                    $summary['skipped']++;

                    continue;
                }

                Invitation::create([
                    'event_id' => $event->id,
                    'guest_id' => $guest->id,
                    'code' => $this->codeGenerator->generate(),
                    'status' => InvitationStatus::Pending,
                ]);

                $summary['created']++;
            }
        });

        return $summary;
    }

    /**
     * @param  Collection<int|string, mixed>  $firstRow
     * @return array{first_name: int|string, last_name: int|string, document_number: int|string}|null
     */
    private function detectHeaderMap(Collection $firstRow): ?array
    {
        $map = [];

        foreach ($firstRow as $key => $value) {
            $label = Str::of((string) $value)
                ->lower()
                ->ascii()
                ->replaceMatches('/[^a-z0-9]+/', '_')
                ->trim('_')
                ->toString();

            if (in_array($label, ['nombre', 'first_name', 'name'], true)) {
                $map['first_name'] = $key;
            }
            if (in_array($label, ['apellido', 'last_name', 'lastname'], true)) {
                $map['last_name'] = $key;
            }
            if (in_array($label, ['dni', 'documento', 'document_number', 'document'], true)) {
                $map['document_number'] = $key;
            }
        }

        if (isset($map['first_name'], $map['last_name'], $map['document_number'])) {
            return $map;
        }

        return null;
    }

    /**
     * @param  Collection<int|string, mixed>  $row
     * @param  array{first_name: int|string, last_name: int|string, document_number: int|string}|null  $headerMap
     * @return array{first_name: string, last_name: string, document_number: string}|null
     */
    private function mapRow(Collection $row, ?array $headerMap): ?array
    {
        if ($headerMap !== null) {
            return [
                'first_name' => trim((string) ($row[$headerMap['first_name']] ?? '')),
                'last_name' => trim((string) ($row[$headerMap['last_name']] ?? '')),
                'document_number' => preg_replace('/\D+/', '', (string) ($row[$headerMap['document_number']] ?? '')) ?: '',
            ];
        }

        $values = array_values($row->all());

        if (count($values) < 3) {
            return null;
        }

        return [
            'first_name' => trim((string) $values[0]),
            'last_name' => trim((string) $values[1]),
            'document_number' => preg_replace('/\D+/', '', (string) $values[2]) ?: '',
        ];
    }
}
