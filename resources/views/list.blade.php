@extends('layouts.app')

@section('content')
<main id="main" class="main">
    <div class="col-12">
        <div class="card recent-sales overflow-auto">
            <div class="card-body">
                <h5 class="card-title">Daftar Fasilitas<span>| GA Section</span></h5>
                <div class="btn-group mb-2" role="group" aria-label="Basic outlined example">
                    <button type="button" class="btn btn-primary"><i class="bi bi-plus-square"></i></button>
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-upload"></i></button>
                    <button type="button" class="btn btn-primary"><i class="bi bi-download"></i></button>
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-trash"></i></button>
                </div>
                <table class="table table-hover table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Properti</th>
                            <th scope="col">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>1</th>
                            <td>Air Conditioner (AC)</td>
                            <td>100</td>
                        </tr>
                        <tr>
                            <th>2</a></th>
                            <td>Vacuum Cleaner</td>
                            <td>20</td>
                        </tr>
                        <tr>
                            <th>3</a></th>
                            <td>Lemari Besi</td>
                            <td>140</td>
                        </tr>
                        <tr>
                            <th>4</a></th>
                            <td>Lemari Kayu</td>
                            <td>150</td>
                        </tr>
                        <tr>
                            <th>5</a></th>
                            <td>Meja Rapat</td>
                            <td>14</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</main>
@endsection