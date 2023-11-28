@extends('layouts.app')

@section('content')
<main id="main" class="main">
    <div class="search-bar">
        <div class="search-form d-flex align-items-center">
            <input type="text" name="query" id="searchTable" placeholder="Search" title="Enter search keyword">
            <button type="button" title="Search"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="col-12">
        <div class="card recent-sales overflow-auto">
            <div class="card-body">
                <h5 class="card-title">Daftar Properti<span>| GA Section</span></h5>
                <div class="btn-group mb-2" role="group" aria-label="Basic outlined example">
                    <button type="button" class="btn btn-primary" id="addForm"><i class="bi bi-plus-square"></i>&nbsp Tambah</button>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importExcel"><i class="bi bi-upload"></i>&nbsp Unggah</button>
                    <a href="{{ route('export.fasilitas') }}" class="btn btn-primary" role="button"><i class="bi bi-download"></i>&nbsp; Unduh</a>
                    <!-- <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('export.fasilitas') }}'"><i class="bi bi-download"></i>&nbsp Unduh</button> -->
                    <button type="button" class="btn btn-outline-primary"><i class="bi bi-arrow-repeat"></i>&nbsp Update</button>
                    <button type="button" class="btn btn-primary" onclick="showDeleteConfirmation(event, this)"><i class="bi bi-trash"></i>&nbsp Reset</button>
                </div>

                <!-- FORM TAMBAH DATA  -->
                <form action="{{ route('simpan.data.tabel') }}" method="POST" id="formAddData" class="d-none">
                    @csrf
                    <h6>TAMBAH DATA</h6>
                    <div id="formContainer">
                        <div class="form-floating mb-2 d-flex align-items-center">
                            <input type="text" name="name[]" class="form-control input-data me-2" id="floatingInput" placeholder="nama tabel">
                            <label for="floatingInput">nama tabel</label>
                            <button type="button" class="btn btn-warning" style="height: 38px;" onclick="add()"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary py-1 px-2 mb-3">Simpan</button>
                    <button type="button" class="btn btn-secondary py-1 px-2 mb-3" id="closeForm">Cancel</button>
                </form>

                <!-- MODAL UPLOAD DATA  -->
                <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('import.data.tabel') }}" method="post" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header p-2 px-3">
                                    <h5 class="modal-title" id="importExcelLabel" style="font-size: 18px;color:#000;">Import Data Excel</h5>
                                </div>
                                <div class="modal-body px-3 pt-2 pb-1 mb-0">
                                    <!-- Tempatkan form import di sini -->
                                    @csrf
                                    <div class="form-group p-0">
                                        <input type="file" name="file" accept=".xlsx, .xls, .csv">
                                    </div>
                                </div>
                                <div class="modal-footer p-1">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <table class="table table-hover table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Properti</th>
                            <th scope="col">Daftar Kolom</th>
                            <th scope="col">Jumlah Atribut</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php $i=1 @endphp
                        @foreach($list as $l)
                        <tr>
                            <th>{{ $i++ }}</th>
                            <td>{{ $l->name }}</td>
                            <td>{{ implode(', ', json_decode($l->columns, true)) }}</td>
                            <td>{{ $l->jumlah_atribut }}</td>
                            <td>{{ $l->jumlah }}</td>
                            <td>
                                <form action="{{ route('delete.table', ['tabel' => $l->name]) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger" style="height: 33px;font-size: 13px;" onclick="confirmDelete(this)"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>
</main>
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script>
    var formAddData = document.getElementById('formAddData');
    var addFormButton = document.getElementById('addForm');
    var closeFormButton = document.getElementById('closeForm');

    addFormButton.addEventListener('click', function() {
        formAddData.classList.remove('d-none');
    });

    closeFormButton.addEventListener('click', function() {
        formAddData.classList.add('d-none');
    });

    var formContainer = document.getElementById('formContainer');

    function add() {
        var newContainer = document.createElement('div');
        newContainer.className = 'form-floating mb-2 d-flex align-items-center';

        var newField = document.createElement('input');
        newField.setAttribute('type', 'text');
        newField.setAttribute('name', 'name[]');
        newField.setAttribute('class', 'form-control input-data me-2');
        newField.setAttribute('id', 'floatingInput');
        newField.setAttribute('placeholder', 'Nama Tabel');
        newContainer.appendChild(newField);

        var newLabel = document.createElement('label');
        newLabel.setAttribute('for', 'floatingInput');
        newLabel.textContent = 'nama tabel';
        newContainer.appendChild(newLabel)

        var newButton = document.createElement('button');
        newButton.setAttribute('type', 'button');
        newButton.setAttribute('class', 'btn btn-secondary');
        newButton.setAttribute('style', 'height: 38px;');
        newButton.innerHTML = '<i class="bi bi-x-lg"></i>';
        newButton.setAttribute('onclick', 'remove()');
        newContainer.appendChild(newButton);

        formContainer.appendChild(newContainer);
    }

    function remove() {
        var input_tags = formContainer.getElementsByTagName('div');
        if (input_tags.length > 1) {
            formContainer.removeChild(input_tags[(input_tags.length) - 1]);
        }
    }
</script>
<script type="text/javascript">
    function showDeleteConfirmation(event, button) {
        event.preventDefault();
        var form = $(button).closest("form");
        swal.fire({
                title: `Apakah anda yakin menghapus semua data ini?`,
                text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            })
            .then((willDelete) => {
                if (willDelete.isConfirmed) {
                    $.get("{{ url('reset-properti') }}", function(data) {
                        location.reload();
                    });
                }
            });
    }
</script>
<script>
    function confirmDelete(button) {
        var form = $(button).closest("form");
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
<script>
    document.getElementById('searchTable').addEventListener('input', function() {
        filterData();
    });

    function filterData() {
        const selected = document.getElementById('searchTable').value;

        fetch(`{{ route('search.table') }}?table=${selected}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('tableBody').innerHTML = data;
            });
    }
</script>
@endsection