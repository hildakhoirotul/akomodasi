@extends('layouts.app')
@section('title', $fasilitas)
@section('content')
<main id="main" class="main">
    <div class="search-bar">
        <div class="search-form d-flex align-items-center">
            <input type="text" name="query" id="searchData" placeholder="Search" title="Enter search keyword">
            <button type="button" title="Search"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <div class="col-12">    
        <div class="card recent-sales">
            <div class="card-body">
                <h5 class="card-title" style="font-size: 22px;">{{ $fasilitas }} &nbsp<span>| GA Section</span></h5>
                @if(Auth::user()->role == 'admin')
                <div class="d-flex justify-content-between table-header mb-1">
                    <form action="{{ route('reset.data', $nama_tabel) }}" method="POST" style="display: inline-block;">
                        <div class="btn-group mb-2" role="group" aria-label="Basic outlined example">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addData"><i class="bi bi-plus-square"></i>&nbsp Tambah Data</button>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importData"><i class="bi bi-upload"></i>&nbsp Unggah</button>
                            <a href="{{ route('export.data', ['nama_tabel' => $nama_tabel]) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i>&nbsp Unduh
                            </a>
                            @csrf
                            @method('DELETE')
                            <a href="{{ route('export.template', ['nama_tabel' => $nama_tabel]) }}" class="btn btn-outline-primary">
                                <i class="bi bi-download"></i>&nbsp; Template
                            </a>
                            <button type="button" class="btn btn-primary" onclick="showResetConfirmation('{{ $nama_tabel }}', event, this)"><i class="bi bi-trash"></i>&nbsp Reset</button>
                        </div>
                    </form>
                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addColumn"><i class="bi bi-plus-lg"></i>&nbsp Tambah Kolom</button>
                        <div class="btn-group">
                            <button class="btn btn-danger" data-intro="ini adalah grup tombol" data-step="1" type="button" id="deleteColumn">
                                <i class="bi bi-x-circle"></i>&nbsp Hapus Kolom
                            </button>
                            <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" id="columnDropdown">
                                @foreach($columns as $c)
                                @if ($c !== 'id' && $c !== 'created_at' && $c !== 'updated_at')
                                <li><a class="dropdown-item" href="#" data-column="{{ $c }}">{{ $c }}</a></li>
                                @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="modal fade" id="addColumn" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true" data-backdrop="false">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('add.column', $nama_tabel)}}" method="POST">
                            <div class="modal-content">
                                <div class="modal-header p-2 px-3" style="background-color: #012970;">
                                    <h5 class="modal-title text-center" id="importExcelLabel" style="color: #fff;">Tambah Kolom</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body justify-content-center p-3 pe-1 mb-0">
                                    @csrf
                                    <div id="formContainer">
                                        <div class="form-floating mb-2 d-flex align-items-center">
                                            <input type="text" name="column[]" class="form-control input-data me-2" id="floatingInput" placeholder="nama kolom">
                                            <label for="floatingInput">nama kolom</label>
                                            <div class="dropdown ms-1 me-2">
                                                <select id="floatingInput" name="tipe[]" class="form-control col-md-3">
                                                    <option value="">-Tipe Data-</option>
                                                    <option value="string">Teks Singkat</option>
                                                    <option value="integer">Angka</option>
                                                    <option value="date">Tanggal</option>
                                                    <option value="time">Waktu</option>
                                                    <option value="double">Desimal</option>
                                                    <option value="boolean">OK/NOT OK</option>
                                                    <option value="text">Teks Panjang</option>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-warning" style="height: 38px;" onclick="addColumn()"><i class="bi bi-plus"></i></button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary py-1 px-2 mt-3" style="background-color: #012970;">Simpan</button>
                                    <button type="button" class="btn btn-secondary py-1 px-2 mt-3" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="addData" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true" data-backdrop="false">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('store.data', $nama_tabel)}}" method="POST">
                            <div class="modal-content">
                                <div class="modal-header p-2 px-3" style="background-color: #012970;">
                                    <h5 class="modal-title text-center" id="importExcelLabel" style="color: #fff;">Tambah Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body justify-content-center p-3 mb-0">
                                    @csrf
                                    @foreach($columns as $column)
                                    @if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at')
                                    <div class="mb-3">
                                        <label for="{{ $column }}" class="form-label">{{ ucfirst($column) }}</label>
                                        @if ($columnTypes[$column] === 'date')
                                        <input type="date" class="form-control" id="{{ $column }}" name="{{ $column }}">
                                        @elseif ($columnTypes[$column] === 'boolean')
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $column }}" id="{{ $column }}" value="1" checked>
                                            <label class="form-check-label" for="{{ $column }}">
                                                OK
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $column }}" id="{{ $column }}" value="0">
                                            <label class="form-check-label" for="{{ $column }}">
                                                NOT OK
                                            </label>
                                        </div>
                                        @elseif ($columnTypes[$column] === 'time')
                                        <input type="time" class="form-control" id="{{ $column }}" name="{{ $column }}">
                                        @elseif ($columnTypes[$column] === 'integer')
                                        <input type="number" class="form-control" id="{{ $column }}" name="{{ $column }}">
                                        @elseif ($columnTypes[$column] === 'text')
                                        <textarea class="form-control" id="{{ $column }}" name="{{ $column }}"></textarea>
                                        @else
                                        <input type="text" class="form-control" id="{{ $column }}" name="{{ $column }}">
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                    <button type="submit" class="btn btn-primary py-1 px-2 mt-3" style="background-color: #012970;">Simpan</button>
                                    <button type="button" class="btn btn-secondary py-1 px-2 mt-3" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="importData" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('import.data', $nama_tabel) }}" method="post" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header p-2 px-3">
                                    <h5 class="modal-title" id="importExcelLabel" style="font-size: 18px;color:#000;">Import Data Excel</h5>
                                </div>
                                <div class="modal-body px-3 pt-2 pb-1 mb-0">
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
                        <tr class="align-middle">
                            <th>No</th>
                            @foreach($columns as $c)
                            @if ($c !== 'id' && $c !== 'created_at' && $c !== 'updated_at')
                            <th scope="col">{{ $c }}</th>
                            @endif
                            @endforeach
                            @if(Auth::user()->role == 'admin')
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="dataBody">
                        @foreach($tabel as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            @foreach($columns as $column)
                            @if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at')
                            <td>
                                @if ($columnTypes[$column] === 'boolean')
                                @if ($row->$column)
                                <span class="badge bg-success">OK</span>
                                @else
                                <span class="badge bg-danger">NOT OK</span>
                                @endif
                                @else
                                {{ $row->$column }}
                                @endif
                            </td>
                            @endif
                            @endforeach
                            @if(Auth::user()->role == 'admin')
                            <td>
                                <form action="{{ route('delete.data', [$nama_tabel, $row->id]) }}" method="POST" style="display: inline-block;">
                                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editData{{ $row->id }}"><i class="bi bi-pen-fill"></i></button>
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ $nama_tabel }}', '{{ $row->id }}', this)"><i class="bi bi-trash-fill"></i></button>
                                    </div>
                                </form>

                                <div class="modal fade" id="editData{{ $row->id }}" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true" data-backdrop="false">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('edit.data', [$nama_tabel, $row->id]) }}" method="POST">
                                            <div class="modal-content">
                                                <div class="modal-header p-2 px-3" style="background-color: #012970;">
                                                    <h5 class="modal-title text-center" id="importExcelLabel" style="color: #fff;">Edit Data</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body justify-content-center p-3 mb-0">
                                                    @csrf
                                                    @foreach($columns as $column)
                                                    @if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at')
                                                    <div class="mb-3">
                                                        <label for="{{ $column }}" class="form-label">{{ ucfirst($column) }}</label>
                                                        @if ($columnTypes[$column] === 'date')
                                                        <input type="date" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $row->$column }}">
                                                        @elseif ($columnTypes[$column] === 'boolean')
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="{{ $column }}" id="{{ $column }}" value="1" {{ $row->$column ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $column }}">
                                                                OK
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="{{ $column }}" id="{{ $column }}" value="0" {{ !$row->$column ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $column }}">
                                                                NOT OK
                                                            </label>
                                                        </div>
                                                        @elseif ($columnTypes[$column] === 'time')
                                                        <input type="time" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $row->$column }}">
                                                        @elseif ($columnTypes[$column] === 'integer')
                                                        <input type="number" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $row->$column }}">
                                                        @elseif ($columnTypes[$column] === 'text')
                                                        <textarea class="form-control" id="{{ $column }}" name="{{ $column }}">{{ $row->$column }}</textarea>
                                                        @else
                                                        <input type="text" class="form-control" id="{{ $column }}" name="{{ $column }}" value="{{ $row->$column }}">
                                                        @endif
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                    <button type="submit" class="btn btn-primary py-1 px-2 mt-3" style="background-color: #012970;">Simpan</button>
                                                    <button type="button" class="btn btn-secondary py-1 px-2 mt-3" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="d-flex justify-content-center mt-3" id="paging">
                {{ $tabel->links()}}
            </div>
        </div>
    </div>
</main>

<script>
    var formContainer = document.getElementById('formContainer');

    function addColumn() {
        var newContainer = document.createElement('div');
        newContainer.className = 'form-floating mb-2 d-flex align-items-center';

        var newField = document.createElement('input');
        newField.setAttribute('type', 'text');
        newField.setAttribute('name', 'column[]');
        newField.setAttribute('class', 'form-control input-data me-2');
        newField.setAttribute('id', 'floatingInput');
        newField.setAttribute('placeholder', 'Nama Tabel');
        newContainer.appendChild(newField);

        var newLabel = document.createElement('label');
        newLabel.setAttribute('for', 'floatingInput');
        newLabel.textContent = 'nama tabel';
        newContainer.appendChild(newLabel)

        var newDiv = document.createElement('div');
        newDiv.setAttribute('class', 'dropdown ms-1 me-2');

        var newDropdown = document.createElement('select');
        newDropdown.setAttribute('id', 'floatingInput');
        newDropdown.setAttribute('name', 'tipe[]');
        newDropdown.setAttribute('class', 'form-control col-md-3');

        var options = [{
                value: "",
                label: "-Tipe Data-"
            },
            {
                value: "string",
                label: "Teks Singkat"
            },
            {
                value: "integer",
                label: "Angka"
            },
            {
                value: "date",
                label: "Tanggal"
            },
            {
                value: "time",
                label: "Waktu"
            },
            {
                value: "double",
                label: "Desimal"
            },
            {
                value: "boolean",
                label: "OK/NOT OK"
            },
            {
                value: "text",
                label: "Teks Panjang"
            }
        ];

        for (var i = 0; i < options.length; i++) {
            var option = document.createElement('option');
            option.value = options[i].value;
            option.text = options[i].label;
            newDropdown.appendChild(option);
        }
        newDiv.appendChild(newDropdown);
        newContainer.appendChild(newDiv);

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
        var input_tags = formContainer.querySelectorAll('.form-floating');
        if (input_tags.length > 1) {
            formContainer.removeChild(input_tags[(input_tags.length) - 1]);
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var columnDropdown = document.getElementById('columnDropdown');

        columnDropdown.addEventListener('click', function(event) {
            var target = event.target;

            if (target.tagName === 'A') {
                var columnToDelete = target.dataset.column;

                Swal.fire({
                    title: 'Konfirmasi Hapus Kolom',
                    text: 'Apakah Anda yakin ingin menghapus kolom ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('delete.column', $nama_tabel) }}";

                        fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                },
                                body: JSON.stringify({
                                    column: columnToDelete
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.success) {
                                    // Reload halaman jika penghapusan berhasil
                                    location.reload();
                                } else {
                                    console.error('Penghapusan gagal:', data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                })

            }
        });
    });
</script>
<script>
    function confirmDelete(table, id, button) {
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
<script type="text/javascript">
    function showResetConfirmation(table, event, button) {
        event.preventDefault();
        var form = $(button).closest("form");
        swal.fire({
                title: `Apakah anda yakin menghapus data ini?`,
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
                    form.submit();
                }
            });
    }
</script>
<script>
    const userRole = @json(Auth::check() ? Auth::user()->role : 'guest');
    console.log(userRole)
    document.getElementById('searchData').addEventListener('input', function() {
        filterData();
    });

    function filterData() {
        const selected = document.getElementById('searchData').value;

        const route = userRole === 1
            ? `{{ route('search.data', $nama_tabel) }}?data=${selected}`
            : `{{ route('search.data.guest', $nama_tabel) }}?data=${selected}`;

        fetch(route)
            .then(response => response.text())
            .then(data => {
                document.getElementById('dataBody').innerHTML = data;
            });
    }
</script>
@endsection