<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Info;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RealRashid\SweetAlert\Facades\Alert;

class InspekturController extends Controller
{
    public function index()
    {
        $list = Fasilitas::get();
        return view('inspektur', compact('list'));
    }

    public function properti($nama_tabel)
    {
        $columns = Schema::getColumnListing($nama_tabel);
        $columnTypes = [];
        $hasBooleanColumn = false;

        foreach ($columns as $column) {
            $columnTypes[$column] = Schema::getColumnType($nama_tabel, $column);
        }

        $info = $this->getInfoTooltip($nama_tabel);

        $hasBooleanColumn = in_array('boolean', $columnTypes);
        $fasilitas = strtoupper(str_replace('_', ' ', $nama_tabel));
        return view('properti', compact('fasilitas', 'nama_tabel', 'columns', 'columnTypes', 'hasBooleanColumn', 'info'));
    }

    public function getInfoTooltip($nama_tabel)
    {
        $infoList = Info::where('table_name', $nama_tabel)->get();

        if ($infoList->isEmpty()) {
            return 'Tidak ada informasi tambahan.';
        }

        $tooltipContent = $infoList->pluck('desc')->implode('<br>');

        return $tooltipContent;
    }

    public function tambahData(Request $request, $nama_tabel)
    {
        $inputData = $request->all();
        unset($inputData['_token']);

        try {
            foreach ($inputData as $key => $value) {
                $normalizedKey = str_replace('_', ' ', $key);
                unset($inputData[$key]);
                $inputData[$normalizedKey] = $value;
            }
            DB::table($nama_tabel)->insert($inputData);
            Alert::success('Tersimpan', 'Berhasil Menambah Data');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Gagal Menyimpan Data');
            return redirect()->back();
        }
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
        $user->chain = $request->get('password_baru');
        $user->save();
        Auth::logout();

        Alert::success('Password berhasil diubah', 'Silahkan Login kembali');
        return redirect()->route('login')->with('logout', true);
    }
}
