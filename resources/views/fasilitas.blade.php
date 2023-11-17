@extends('layouts.app')

@section('content')
<main id="main" class="main">
    <div class="col-12">
        <div class="card recent-sales overflow-auto">
            <div class="card-body">
                <h5 class="card-title">{{ $fasilitas->name }} &nbsp<span>| GA Section</span></h5>
                <div class="btn-group mb-2" role="group" aria-label="Basic outlined example">
                    <button type="button" class="btn btn-primary" id="addForm"><i class="bi bi-plus-square"></i>&nbsp Tambah</button>
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-upload"></i>&nbsp Unggah</button>
                    <button type="button" class="btn btn-primary"><i class="bi bi-download"></i>&nbsp Unduh</button>
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-trash"></i>&nbsp Reset</button>
                </div>

                <table class="table table-hover table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Month</th>
                            <th scope="col">Cleaning Status</th>
                            <th scope="col">Remark</th>
                            <th scope="col">Vendor Cleaning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>0001</th>
                            <td>Office 1</td>
                            <td>Juli</td>
                            <td>Belum</td>
                            <td><span class="badge bg-danger">Pending</span></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>0002</th>
                            <td>Office 2</td>
                            <td>November</td>
                            <td>Sudah</td>
                            <td><span class="badge bg-success">Finish</span></td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <th>0003</th>
                            <td>Metro 5</td>
                            <td>Januari</td>
                            <td>Sudah</td>
                            <td><span class="badge bg-success">Finish</span></td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</main>
@endsection