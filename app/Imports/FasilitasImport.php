<?php

namespace App\Imports;

use App\Models\Fasilitas;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class FasilitasImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use Importable, SkipsFailures;
    protected $errors = [];

    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }

    public function model(array $row)
    {
        return new Fasilitas([
            'name' => $row['name'],
        ]);
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function withValidation($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                $this->errors[] = $validator->errors()->all();
            }
        });
    }

    public function customValidationMessage(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong.',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
}
