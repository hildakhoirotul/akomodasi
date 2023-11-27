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

Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index')->name('home');
    Route::get('/daftar-fasilitas', 'listFasilitas')->name('list.fasilitas');
    Route::get('/fasilitas/{nama_tabel}', 'fasilitas')->name('fasilitas');
    Route::post('/simpan-fasilitas', 'simpanDataTabel')->name('simpan.data.tabel');
    Route::post('/import-data', 'importData')->name('import.data.tabel');
    Route::get('/unduh-properti', 'exportFasilitas')->name('export.fasilitas');
    Route::get('/reset-properti', 'resetFasilitas')->name('reset.fasilitas');
    Route::post('/tambah-kolom/{nama_tabel}', 'addColumn')->name('add.column');
    Route::post('/hapus-kolom/{nama_tabel}', 'deleteColumn')->name('delete.column');
    Route::post('/tambah-data/{nama_tabel}', 'tambahData')->name('store.data');
    Route::delete('/hapus-tabel/{tabel}', 'hapusTabel')->name('delete.table');
    Route::delete('/hapus-data/{tabel}/{id}', 'hapusData')->name('delete.data');
    Route::post('/edit-data/{tabel}/{id}', 'editData')->name('edit.data');
    Route::post('/import-data/{nama_tabel}', 'importDataColumn')->name('import.data');
    Route::get('/export-data/{nama_tabel}', 'exportDataColumn')->name('export.data');
    Route::get('/export-template/{nama_tabel}', 'exportTemplateColumn')->name('export.template');
    Route::delete('/reset-data/{nama_tabel}', 'resetDataColumn')->name('reset.data');
});
