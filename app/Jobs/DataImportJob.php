<?php

namespace App\Jobs;

use App\Imports\DataImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class DataImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $path;
    protected $nama_tabel;
    protected $columns;
    public function __construct($path, $nama_tabel, $columns)
    {
        $this->path = $path;
        $this->nama_tabel = $nama_tabel;
        $this->columns = $columns;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $import = new DataImport($this->nama_tabel, $this->columns);
        Excel::import($import, $this->path);
        Storage::delete($this->path);

        $errors = $import->getErrors();

        if (!empty($errors)) {
            $error = implode(" ", $errors);
            Alert::html('<small>Impor Gagal</small>', '<small>Error pada: <br>' . $error, 'error')->width('600px');
            return redirect()->back();
        } else {
            Alert::success('Impor Berhasil', ' Berhasil diimpor');
            return redirect()->back();
        }
        // if($import) {
        //     Alert::success('Berhasil', 'Unggah data berhasil');
        //     return redirect()->back();
        // } else {
        //     Alert::error('Gagal', 'Unggah data gagal');
        //     return redirect()->back();
        // }
    }
}
