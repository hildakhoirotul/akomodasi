<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FasilitasExport implements FromArray, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nama Properti',
            'Daftar Kolom',
            'Jumlah Atribut',
            'Jumlah',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }

    public function map($row): array
    {
        $columns = json_decode($row['columns'], true);

        if (!is_array($columns)) {
            $columns = [];
        }

        return [
            $row['name'],
            implode(',', $columns),
            strval($row['jumlah_atribut']),
            strval($row['jumlah']),
        ];
        // return [
        //     $row['name'],
        //     implode(',', $row['columns']),
        //     strval($row['jumlah_atribut']),
        //     strval($row['jumlah']),
        // ];
    }
}
