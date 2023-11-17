<?php

namespace App\Http\Controllers;

use App\Exports\FasilitasExport;
use App\Jobs\FasilitasImportJob;
use App\Models\Fasilitas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $list = Fasilitas::get();
        return view('home', compact('list'));
    }

    public function listFasilitas()
    {
        $list = Fasilitas::get();
        return view('list', compact('list'));
    }

    public function simpanDataTabel(Request $request)
    {
        $request->validate([
            'name.*' => 'required|string',
        ]);

        foreach ($request->input('name') as $name) {
            Fasilitas::create([
                'name' => $name,
            ]);

            $namaTabel = str_replace(' ', '_', strtolower($name));

            Schema::create($namaTabel, function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });

            Artisan::call('make:model', [
                'name' => ucfirst($namaTabel),
            ]);
        }
        Artisan::call('migrate');

        return redirect()->back();
    }

    public function importData(Request $request)
    {
        $file = $request->file('file');
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $nama_file = rand() . $file->getClientOriginalName();

        $path = $file->storeAs('public/excel/', $nama_file);

        FasilitasImportJob::dispatch($path)->onQueue('impor_fasilitas');
        $table = Schema::getAllTables();
        $fasName = Fasilitas::pluck('name')->toArray();

        $TransFasName = array_map(function ($nama) {
            $nama = str_replace(' ', '_', $nama);

            $nama = strtolower($nama);

            return $nama;
        }, $fasName);

        $tableNames = array_map(function ($table) {
            return $table->Tables_in_akomodasi;
        }, $table);
        $newData = array_diff($TransFasName, $tableNames);

        foreach ($newData as $data) {
            Schema::create($data, function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });

            Artisan::call('make:model', [
                'name' => ucfirst($data),
            ]);
        }

        Artisan::call('migrate');

        return redirect()->back();
    }

    public function exportFasilitas()
    {
        $data = Fasilitas::all()->toArray();
        return Excel::download(new FasilitasExport($data), 'daftar_properti.xlsx');
    }

    public function resetFasilitas()
    {
        $fasName = Fasilitas::pluck('name')->toArray();

        $transFasName = array_map(function ($nama) {
            $nama = str_replace(' ', '_', $nama);

            $nama = strtolower($nama);

            return $nama;
        }, $fasName);
        foreach ($transFasName as $name){
            DB::statement("DROP TABLE $name");
        }
        Fasilitas::truncate();
        return redirect()->back();
    }

    public function fasilitas($nama_tabel)
    {
        $list = Fasilitas::get();
        $fasilitas = Fasilitas::where('name', 'LIKE', '%' . $nama_tabel . '%')->first();

        return view('fasilitas', compact('list','nama_tabel','fasilitas'));
    }
}
