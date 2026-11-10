<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegionReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected Collection $data,
    ) {}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Filial (Hudud)',
            'Zakazlar soni',
            'Jami summa (so\'m)',
            'O\'rtacha chek (so\'m)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->orders_count,
            (float) $row->total_amount,
            round((float) $row->average_amount, 2),
        ];
    }
}
