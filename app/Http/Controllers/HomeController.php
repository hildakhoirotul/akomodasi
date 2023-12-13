<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Exports\FasilitasExport;
use App\Exports\TemplateExport;
use App\Jobs\DataImportJob;
use App\Jobs\FasilitasImportJob;
use App\Models\Activity;
use App\Models\Fasilitas;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

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
        $activity = Activity::orderBy('created_at', 'desc')->take(10)->get();
        $latest = Activity::whereNotNull('tabel')->latest()->take(6)->get();
        $latest = Activity::selectRaw('MAX(id) as id, tabel, MAX(created_at) as created_at')
            ->whereNotNull('tabel')
            ->where('tabel', '<>', 'users')
            ->groupBy('tabel')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $tableCounts = [];
        $trueCount = [];
        $falseCount = [];
        $totalFalse = [];
        $totalTrue = [];
        foreach ($latest as $item) {
            $tableName = $item->tabel;
            if ($tableName === 'users') {
                continue;
            }
            $tableCounts[$tableName] = DB::table($tableName)->count();

            $booleanColumns = $this->getBooleanColumn($tableName);
            if (!empty($booleanColumns)) {
                foreach ($booleanColumns as $columnName) {
                    if (!isset($falseCount[$tableName][$columnName])) {
                        $falseCount[$tableName][$columnName] = 0;
                    }

                    $falseCount[$tableName][$columnName] += DB::table($tableName)
                        ->where($columnName, 0)
                        ->count();
                }

                $trueCount[$tableName] = $tableCounts[$tableName] - array_sum($falseCount[$tableName]);
                $totalFalse[$tableName] = array_sum($falseCount[$tableName]);
                $totalTrue[$tableName] = $tableCounts[$tableName] - $totalFalse[$tableName];
            }
        }
        return view('home', compact('list', 'activity', 'latest', 'tableCounts', 'totalFalse', 'totalTrue'));
    }

    public function listFasilitas()
    {
        $list = Fasilitas::paginate(50);
        foreach ($list as $fasilitas) {
            $tableName = str_replace(' ', '_', strtolower($fasilitas->name));
            // $jumlahAtribut = count(json_decode($fasilitas->columns, true));
            $decodedColumns = json_decode($fasilitas->columns, true);
            $jumlahAtribut = is_array($decodedColumns) ? count($decodedColumns) : 0;
            $jumlahData = DB::table($tableName)->count();

            $fasilitas->jumlah_atribut = $jumlahAtribut;
            $fasilitas->jumlah = $jumlahData;
            $fasilitas->save();
        }

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
        }
        Artisan::call('migrate');

        return redirect()->back();
    }

    public function hapusTabel($tabel)
    {
        DB::table('Fasilitas')->where('name', $tabel)->delete();

        $namaTabel = str_replace(' ', '_', strtolower($tabel));
        DB::statement("DROP TABLE $namaTabel");
        DB::table('activities')->where('tabel', $namaTabel)->update(['tabel' => null]);

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
        $fasilitas = strtoupper(str_replace('_', ' ', $nama_tabel));

        $queryBuilder = DB::table($nama_tabel);
        $tabel = $queryBuilder->paginate(50);

        $columns = Schema::getColumnListing($nama_tabel);
        $columnTypes = [];
        $hasBooleanColumn = false;

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($nama_tabel, $column);
        }

        $hasBooleanColumn = in_array('boolean', $columnTypes);

        return view('fasilitas', compact('list', 'fasilitas', 'tabel', 'columns', 'nama_tabel', 'columnTypes', 'hasBooleanColumn'));
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
        $currentColumns = json_decode($fasilitas->columns, true);

        $index = array_search($column, $currentColumns);
        if ($index !== false) {
            unset($currentColumns[$index]);
        }

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
        unset($inputData['_token']);

        foreach ($inputData as $key => $value) {
            $normalizedKey = str_replace('_', ' ', $key);
            unset($inputData[$key]);
            $inputData[$normalizedKey] = $value;
        }
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
        $file = $request->file('file');
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $nama_file = rand() . $file->getClientOriginalName();
        $path = $file->storeAs('public/excel/', $nama_file);
        DataImportJob::dispatch($path, $nama_tabel, $columns)->onQueue('impor_data');
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

    public function searchTable(Request $request)
    {
        $searchTerm = $request->input('table');
        $query = Fasilitas::query();

        if ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('jumlah_atribut', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('jumlah', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere(function ($q) use ($searchTerm) {
                    $q->whereRaw("LOWER(columns) LIKE ?", ['%' . strtolower($searchTerm) . '%']);
                });
        }

        $result = $query->get();
        return view('partial.list', ['result' => $result]);
    }

    public function searchData(Request $request, $nama_tabel)
    {
        $columns = Schema::getColumnListing($nama_tabel);
        $columnTypes = [];

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($nama_tabel, $column);
        }

        $searchTerm = $request->input('data');
        $query = DB::table($nama_tabel);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm, $columns) {
                foreach ($columns as $column) {
                    if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at') {
                        $q->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                    }
                }
            });
        }
        $tabel = $query->get();

        return view('partial.fasilitas', compact('tabel', 'columns', 'columnTypes', 'nama_tabel'));
    }

    private function getBooleanColumn($nama_tabel)
    {
        $columns = Schema::getColumnListing($nama_tabel);
        $booleanColumns = [];

        foreach ($columns as $column) {
            if ($this->isBooleanColumn($nama_tabel, $column)) {
                $booleanColumns[] = $column;
            }
        }

        return $booleanColumns;
    }

    private function isBooleanColumn($tableName, $column)
    {
        $columnType = DB::getSchemaBuilder()->getColumnType($tableName, $column);
        return $columnType === 'boolean' || $columnType === 'tinyint(1)';
    }

    public function changePassword()
    {
        return response()->view('auth.change');
    }

    public function gantiSandi(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string|min:6',
            'password_baru' => 'required|string|min:6',
            'password_confirm' => 'required|string|min:6'
        ]);

        if (!(Hash::check($request->get('password_lama'), Auth::user()->password))) {
            Alert::error('Gagal', 'Password lama salah')->persistent(true, false);
            return redirect()->back();
        }

        if (strcmp($request->get('password_lama'), $request->get('password_baru')) == 0) {
            Alert::error('Gagal', 'Password baru tidak boleh sama dengan Password lama')->persistent(true, false);
            return redirect()->back();
        }
        if (strcmp($request->get('password_baru'), $request->get('password_confirm')) !== 0) {
            Alert::error('Gagal', 'Password baru harus sama dengan Konfirmasi password')->persistent(true, false);
            return redirect()->back();
        }
        $user = Auth::user();
        $user->password = bcrypt($request->get('password_baru'));
        $user->save();
        Auth::logout();

        Alert::success('Password berhasil diubah', 'Silahkan Login kembali');
        return redirect()->route('login')->with('logout', true);
    }

    public function daftarAdmin()
    {
        $admin = User::where('role', '<>', 'guest')->get();
        $list = Fasilitas::get();
        return view('user', compact('admin', 'list'));
    }

    public function addAdmin(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'name' => 'required|string',
            'role' => 'required|string',
            'password' => 'required',
        ]);

        $data = new User();
        $data->nik = $request->nik;
        $data->name = $request->name;
        $data->role = $request->role;
        $data->chain = $request->password;
        $data->password = Hash::make($request->password);
        $data->save();

        Alert::success('Berhasil', 'Data telah tersimpan.');
        return redirect()->route('daftar.admin');
    }

    public function searchAdmin(Request $request)
    {
        $searchTerm = $request->input('search');
        $query = User::query();
        if ($searchTerm) {
            $query->where('role', '<>', 'guest')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('nik', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('name', 'LIKE', '%' . $searchTerm . '%');
                });
        } else {
            $query->where('role', '<>', 'guest');
        }

        $data = $query->get();

        return view('partial.user', ['data' => $data]);
    }

    public function editAdmin(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required|string',
            'name' => 'required|string',
        ]);

        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        $user->nik = $request->input('nik');
        $user->name = $request->input('name');
        $user->role = $request->input('role');
        $user->chain = $request->input('password');
        $user->password = bcrypt($request->input('password'));

        $user->save();

        return redirect()->back();
    }

    public function deleteAdmin($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->back();
    }
}
