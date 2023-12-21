<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\Failure;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class DataImport implements ToCollection, WithHeadingRow, WithBatchInserts, SkipsOnFailure
{
    /**
     * @param Collection $collection
     */

    use SkipsFailures;

    protected $tableName;
    protected $columns;
    private $columnTypes;
    protected $errors = [];

    public function __construct($tableName, $columns)
    {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->columnTypes = $this->getColumnTypes();
    }

    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row) {
            $data = [];

            foreach ($this->columns as $column) {
                if (!in_array($column, ['id', 'updated_at', 'created_at'])) {
                    $format = str_replace('-', '_', $column);
                    $formatted1 = strtolower(str_replace(' ', '_', $format));
                    $formatted2 = strtolower(preg_replace('/[^\w\s]/', '', $formatted1));
                    $formatted = str_replace('__', '_', $formatted2);

                    if ($this->columnTypes[$column] === 'date') {
                        // $dataValue = intval($row[$formatted]);
                        // $value = Date::excelToDateTimeObject($dataValue)->format('Y-m-d');
                        $dataValue = $row[$formatted];
                        if ($dataValue === null || $dataValue === "-" || $dataValue === 0) {
                            $value = null;
                        } else {
                            $dataValue = intval($dataValue);
                            $value = Date::excelToDateTimeObject($dataValue)->format('Y-m-d');
                        }
                    } elseif ($this->columnTypes[$column] === 'time') {
                        // $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[$formatted])->format('H:i:s');
                        $dataValue = $row[$formatted];
                        if ($dataValue === null || $dataValue === "-" || $dataValue === 0) {
                            $value = null;
                        } else {
                            $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dataValue)->format('H:i:s');
                        }
                    } elseif ($this->columnTypes[$column] === 'boolean') {
                        // $value = (strtolower($row[$formatted]) === 'ok') ? 1 : ((strtolower($row[$formatted]) === 'not ok') ? 0 : $row[$formatted]);
                        $dataValue = $row[$formatted];

                        if ($dataValue === null || $dataValue === "-" || $dataValue === 0) {
                            $value = 0;
                        } else {
                            $value = (strtolower($dataValue) === 'ok') ? 1 : ((strtolower($dataValue) === 'not ok') ? 0 : $dataValue);
                        }
                    } else {
                        $value = $row[$formatted];
                    }
                    // if ($this->columnTypes[$column] === 'date') {
                    //     $dataValue = intval($row[$formatted]);
                    //     $dateTimeObject = Date::excelToDateTimeObject($dataValue);

                    //     if ($dateTimeObject == Date::excelToDateTimeObject(0)) {
                    //         $this->errors[] = "Kesalahan Pada '{$row}', Failed to process date in column '{$column}': Invalid date value";
                    //         continue;
                    //     }

                    //     $value = $dateTimeObject->format('Y-m-d');
                    // } elseif ($this->columnTypes[$column] === 'time') {
                    //     $dateTimeObject = Date::excelToDateTimeObject($row[$formatted]);
                    //     $dataValue = intval($row[$formatted]);
                    //     $dateTimeObject = Date::excelToDateTimeObject($dataValue);

                    //     if ($dateTimeObject == Date::excelToDateTimeObject(0)) {
                    //         $this->errors[] = "Kesalahan Pada '{$row}', Failed to process time in column '{$column}': Invalid time value";
                    //         continue;
                    //     }

                    //     $value = $dateTimeObject->format('H:i:s');
                    // } else {
                    //     $value = (strtolower($row[$formatted]) === 'ok') ? 1 : ((strtolower($row[$formatted]) === 'not ok') ? 0 : $row[$formatted]);
                    // }
                    $data[$column] = $value;
                }
            }
            DB::table($this->tableName)->insert($data);
        }
    }

    public function batchSize(): int
    {
        return 500;
    }

    private function getColumnTypes()
    {
        $columnTypes = [];

        $columns = Schema::getColumnListing($this->tableName);

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($this->tableName, $column);
        }

        return $columnTypes;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
