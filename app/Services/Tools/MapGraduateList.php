<?php

namespace App\Services\Tools;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class MapGraduateList
{
    /**
     * @return list<array{0: string, 1: int, 2: string, 3: string}>
     */
    public function rows(UploadedFile|string $file): array
    {
        /** @var Collection<int, Collection<int, Collection<int|string, mixed>>> $sheets */
        $sheets = Excel::toCollection(null, $file);
        $sheet = $sheets->first() ?? collect();

        /** @var array<string, array{name: string, mesa: string, count: int, minors: int}> $groups */
        $groups = [];

        foreach ($sheet as $index => $row) {
            $cells = collect($row);
            $graduate = $this->text($cells->get(0));
            $type = $this->text($cells->get(2));
            $table = $this->text($cells->get(4));

            if ($graduate === '') {
                continue;
            }

            if ($index === 0 && $this->isHeader($graduate)) {
                continue;
            }

            $key = $graduate."\0".$table;

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'name' => $graduate,
                    'mesa' => $table,
                    'count' => 0,
                    'minors' => 0,
                ];
            }

            $groups[$key]['count']++;

            if ($this->isMinor($type)) {
                $groups[$key]['minors']++;
            }
        }

        return array_map(fn (array $group) => [
            $group['name'],
            $group['count'],
            $group['mesa'],
            $this->observations($group['minors']),
        ], array_values($groups));
    }

    private function isHeader(string $graduate): bool
    {
        return str_contains(mb_strtolower($graduate), 'egresad');
    }

    private function isMinor(string $type): bool
    {
        return mb_strtolower($type) === 'menor';
    }

    private function observations(int $minors): string
    {
        return match (true) {
            $minors === 1 => '1 menor',
            $minors > 1 => $minors.' menores',
            default => '',
        };
    }

    private function text(mixed $value): string
    {
        if (is_int($value) || is_float($value)) {
            if (is_float($value) && floor($value) === $value) {
                return (string) (int) $value;
            }

            return trim((string) $value);
        }

        return trim((string) $value);
    }
}
