<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::controller(HomeController::class)->middleware(['web', 'activity'])->group(function () {
    Route::get('/home', 'index')->name('home');
    Route::get('/daftar-fasilitas', 'listFasilitas')->name('list.fasilitas');
    Route::get('/fasilitas/{nama_tabel}', 'fasilitas')->name('fasilitas');
    Route::post('/tambah-tabel', 'simpanDataTabel')->name('simpan.data.tabel');
    Route::post('/import-tabel', 'importData')->name('import.data.tabel');
    Route::get('/unduh-properti', 'exportFasilitas')->name('export.fasilitas');
    Route::delete('/reset-daftar-tabel', 'resetFasilitas')->name('reset.fasilitas');
    Route::post('/tambah-kolom-di/{nama_tabel}', 'addColumn')->name('add.column');
    Route::delete('/hapus-kolom-di/{nama_tabel}', 'deleteColumn')->name('delete.column');
    Route::post('/tambah-data-di/{nama_tabel}', 'tambahData')->name('store.data');
    Route::delete('/hapus-tabel/{tabel}', 'hapusTabel')->name('delete.table');
    Route::delete('/hapus-data-di/{tabel}/{id}', 'hapusData')->name('delete.data');
    Route::post('/edit-data-di/{tabel}/{id}', 'editData')->name('edit.data');
    Route::post('/import-data-di/{nama_tabel}', 'importDataColumn')->name('import.data');
    Route::get('/export-data/{nama_tabel}', 'exportDataColumn')->name('export.data');
    Route::get('/export-template/{nama_tabel}', 'exportTemplateColumn')->name('export.template');
    Route::delete('/reset-data/{nama_tabel}', 'resetDataColumn')->name('reset.data');
    Route::get('/search-table', 'searchTable')->name('search.table');
    Route::get('/search-data/{nama_tabel}', 'searchData')->name('search.data');
    Route::get('/change-password', 'changePassword')->name('gantiSandi');
    Route::post('/ganti-sandi', 'gantiSandi')->name('changePassword');
});
