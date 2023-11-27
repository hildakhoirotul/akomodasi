<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Exports\FasilitasExport;
use App\Exports\TemplateExport;
use App\Imports\DataImport;
use App\Jobs\DataImportJob;
use App\Jobs\FasilitasImportJob;
use App\Models\Fasilitas;
use Illuminate\Database\Eloquent\Builder;
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

            // Artisan::call('make:model', [
            //     'name' => ucfirst($namaTabel),
            // ]);
        }
        Artisan::call('migrate');

        return redirect()->back();
    }

    public function hapusTabel($tabel)
    {
        DB::table('Fasilitas')->where('name', $tabel)->delete();

        $namaTabel = str_replace(' ', '_', strtolower($tabel));
        DB::statement("DROP TABLE $namaTabel");
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

            // Artisan::call('make:model', [
            //     'name' => ucfirst($data),
            // ]);
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
        foreach ($transFasName as $name) {
            DB::statement("DROP TABLE $name");
        }
        Fasilitas::truncate();
        return redirect()->back();
    }

    public function fasilitas($nama_tabel)
    {
        $list = Fasilitas::get();
        $fasilitas = Fasilitas::where('name', 'LIKE', '%' . $nama_tabel . '%')->first();

        $queryBuilder = DB::table($nama_tabel);
        $tabel = $queryBuilder->get();

        $columns = Schema::getColumnListing($nama_tabel);
        $columnTypes = [];

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($nama_tabel, $column);
        }

        return view('fasilitas', compact('list', 'fasilitas', 'tabel', 'columns', 'nama_tabel', 'columnTypes'));
    }

    public function addColumn(Request $request, $nama_tabel)
    {
        $request->validate([
            'column' => 'required|array',
            'tipe' => 'required|array',
        ]);

        $column = $request->input('column');
        $tipe = $request->input('tipe');

        $fasilitas = Fasilitas::where('name', 'LIKE', '%' . $nama_tabel . '%')->first();
        foreach ($column as $key => $column) {
            $type = $tipe[$key];

            Schema::table($nama_tabel, function (Blueprint $table) use ($column, $type) {
                $table->{$type}($column)->nullable();
            });

            $current = json_decode($fasilitas->columns, true);
            $current[] = $column;

            $fasilitas->update(['columns' => json_encode($current)]);
        }

        return redirect()->back();
    }

    public function deleteColumn(Request $request, $nama_tabel)
    {
        $column = $request->input('column');

        $fasilitas = Fasilitas::where('name', 'LIKE', '%' . $nama_tabel . '%')->first();
        // dd($fasilitas);
        $currentColumns = json_decode($fasilitas->columns, true);

        // Hapus kolom yang diinginkan
        $index = array_search($column, $currentColumns);
        if ($index !== false) {
            unset($currentColumns[$index]);
        }

        // Simpan kembali data terbaru ke kolom 'columns'
        $fasilitas->columns = json_encode($currentColumns);
        $fasilitas->save();

        Schema::table($nama_tabel, function (Blueprint $table) use ($column) {
            $table->dropColumn($column);
        });

        return response()->json(['success' => true, 'message' => 'Kolom berhasil dihapus']);
    }

    public function tambahData(Request $request, $nama_tabel)
    {
        $inputData = $request->all();
        // dd($inputData);
        unset($inputData['_token']);

        foreach ($inputData as $key => $value) {
            $normalizedKey = str_replace('_', ' ', $key);
            unset($inputData[$key]);
            $inputData[$normalizedKey] = $value;
        }

        // dd($inputData);

        DB::table($nama_tabel)->insert($inputData);
        return redirect()->back();
    }

    public function hapusData($table, $id)
    {
        DB::table($table)->where('id', $id)->delete();

        return redirect()->back();
    }

    public function editData(Request $request, $tabel, $id)
    {
        $data = DB::table($tabel)->find($id);

        if (!$data) {
            abort(404);
        }

        $inputData = $request->all();
        unset($inputData['_token']);
        foreach ($inputData as $key => $value) {
            $normalizedKey = str_replace('_', ' ', $key);
            unset($inputData[$key]);
            $inputData[$normalizedKey] = $value;
        }

        DB::table($tabel)->where('id', $id)->update($inputData);

        return redirect()->back();
    }

    public function importDataColumn(Request $request, $nama_tabel)
    {
        $table = Fasilitas::where('name', 'LIKE', '%' . $nama_tabel . '%')->first();
        $columns = json_decode($table->columns, true);
        // $column = Schema::getColumnListing($nama_tabel);
        $file = $request->file('file');
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $nama_file = rand() . $file->getClientOriginalName();
        $path = $file->storeAs('public/excel/', $nama_file);
        DataImportJob::dispatch($path, $nama_tabel, $columns)->onQueue('impor_data');

        // Excel::import(new DataImport($nama_tabel, $columns), $file);

        return redirect()->back();
    }

    public function exportDataColumn($nama_tabel)
    {
        return Excel::download(new DataExport($nama_tabel), 'data_properti.xlsx');
    }

    public function exportTemplateColumn($nama_tabel)
    {
        return Excel::download(new TemplateExport($nama_tabel), "template_{$nama_tabel}.xlsx");
    }

    public function resetDataColumn($nama_tabel)
    {
        DB::table($nama_tabel)->truncate();
        return redirect()->back();
    }
}
