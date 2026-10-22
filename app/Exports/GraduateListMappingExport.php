<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GraduateListMappingExport implements FromArray, WithHeadings
{
    /**
     * @param  list<array{0: string, 1: int, 2: string, 3: string}>  $rows
     */
    public function __construct(
        private readonly array $rows,
    ) {}

    /**
     * @return list<array{0: string, 1: int, 2: string, 3: string}>
     */
    public function array(): array
    {
        return $this->rows;
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'Nombre Egresado',
            'Cantidad',
            'Mesa',
            'Observaciones',
        ];
    }
}
