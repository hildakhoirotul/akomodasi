<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;
    protected $nama_tabel;

    public function __construct($nama_tabel)
    {
        $this->nama_tabel = $nama_tabel;
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        $allColumns = Schema::getColumnListing($this->nama_tabel);

        $filteredColumns = array_diff($allColumns, ['id', 'updated_at', 'created_at']);

        return $filteredColumns;
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
