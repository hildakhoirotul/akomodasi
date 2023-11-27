<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $nama_tabel;

    public function __construct($nama_tabel)
    {
        $this->nama_tabel = $nama_tabel;
    }

    public function collection()
    {
        $allColumns = Schema::getColumnListing($this->nama_tabel);

        $filteredColumns = array_diff($allColumns, ['id', 'updated_at', 'created_at']);

        $columnTypes = $this->getColumnTypes($this->nama_tabel, $filteredColumns);

        $data = DB::table($this->nama_tabel)->select($filteredColumns)->get();

        $data = $data->map(function ($row) use ($columnTypes) {
            return collect($row)->map(function ($value, $column) use ($columnTypes) {
                if ($this->isBooleanColumn($column, $columnTypes)) {
                    return $value ? 'OK' : 'NOT OK';
                } elseif ($this->isNumericColumn($columnTypes[$column])) {
                    return is_numeric($value) ? (string)$value : $value;
                }
                return $value;
            })->all();
        });

        return $data;
    }

    protected function getColumnTypes($table, $columns)
    {
        $columnTypes = [];

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($table, $column);
        }

        return $columnTypes;
    }

    public function headings(): array
    {
        $allColumns = Schema::getColumnListing($this->nama_tabel);

        $filteredColumns = array_diff($allColumns, ['id', 'updated_at', 'created_at']);

        return $filteredColumns;
    }

    protected function isBooleanColumn($column, $columnTypes)
    {
        return in_array($columnTypes[$column], ['boolean', 'tinyint']);
    }

    protected function isNumericColumn($columnType)
    {
        return in_array($columnType, ['integer', 'double']);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle('thin');
    }
}
