<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Fasilitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuestController extends Controller
{
    public function indexForGuest()
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
        // dd($list);
        foreach ($list as $fasilitas) {
            $tableName = str_replace(' ', '_', strtolower($fasilitas->name));
            $jumlahAtribut = count(json_decode($fasilitas->columns, true));
            $jumlahData = DB::table($tableName)->count();

            $fasilitas->jumlah_atribut = $jumlahAtribut;
            $fasilitas->jumlah = $jumlahData;
            $fasilitas->save();
        }

        return view('list', compact('list'));
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

    public function searchTable(Request $request)
    {
        $searchTerm = $request->input('table');
        $query = Fasilitas::query();

        if ($searchTerm) {
            $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('jumlah_atribut', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('jumlah', 'LIKE', '%' . $searchTerm . '%')
                // ->orWhere(function ($q) use ($searchTerm) {
                //     $q->whereJsonContains('columns', $searchTerm)
                //       ->orWhere('columns', 'LIKE', '%' . $searchTerm . '%');
                // });
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
        // $jenis = $request->input('jenis');
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

        // if ($jenis) {
        //     $booleanColumn = $this->getBooleanColumn($nama_tabel);
        //     $query->where($booleanColumn, '=', $jenis);
        // }

        // if ($searchTerm) {
        //     $query->where(function ($q) use ($searchTerm, $columns, $nama_tabel) {
        //         foreach ($columns as $column) {
        //             if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at') {
        //                 if ($this->isBooleanColumn($nama_tabel, $column)) {
        //                     $q->orWhere(function ($inner) use ($column, $searchTerm) {
        //                         $inner->where($column, '=', strtolower($searchTerm) === 'ok' ? 1 : 0)
        //                             ->orWhere($column, '=', strtoupper($searchTerm) === 'OK' ? 1 : 0);
        //                     });
        //                 } else {
        //                     $q->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
        //                 }
        //             }
        //         }
        //     });
        // }

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
}
