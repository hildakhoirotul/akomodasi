@extends('layouts.app')
@section('title', 'Daftar Pengguna')

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
                <h5 class="card-title">Daftar Admin<span>| GA Section</span></h5>
                <div class="btn-group mb-2" role="group" aria-label="Basic outlined example">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addForm"><i class="bi bi-plus-square"></i>&nbsp Tambah</button>
                </div>

                <!-- FORM TAMBAH DATA  -->
                <div class="modal fade" id="addForm" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true" data-backdrop="false">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route('add.admin') }}" method="POST">
                            <div class="modal-content">
                                <div class="modal-header p-2 px-3" style="background-color: #012970;">
                                    <h5 class="modal-title text-center" id="importExcelLabel" style="color: #fff;">Tambah Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body justify-content-center p-3 mb-0">
                                    @csrf
                                    <div class="registration-form">
                                        <div class="form-group">
                                            <label for="nik">NIK</label>
                                            <input type="text" class="form-control item mt-1" name="nik" id="nik" placeholder="Type here ...">
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="name">NAMA</label>
                                            <input type="text" class="form-control item mt-1" name="name" id="name" placeholder="Type here ...">
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="role">ROLE</label>
                                            <select class="form-control item mt-1" name="role" id="role">
                                                <option value="admin">Admin</option>
                                                <option value="inspektur">Inspektur</option>
                                            </select>
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="password">PASSWORD</label>
                                            <input type="password" class="form-control item mt-1" name="password" id="password">
                                        </div>
                                        <div class="form-group d-flex justify-content-center mt-3">
                                            <button type="submit" class="btn btn-primary create-account">Simpan</button>
                                            <button type="button" class="btn btn-secondary btn-cancel ms-2" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-hover table-responsive text-center align-middle">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">NIK</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Role</th>
                            <th scope="col">Password</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php $i=1 @endphp
                        @foreach($admin as $a)
                        <tr>
                            <th>{{ $i++ }}</th>
                            <td>{{ $a->nik }}</td>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->role }}</td>
                            <td>
                                <div class="password-container">
                                    <input type="password" class="password-text" data-chain="{{ $a->chain }}" value="{{ $a->chain }}" readonly>
                                    <i class="toggle-password-icon bi bi-eye-slash-fill" onclick="togglePasswordVisibility(this)"></i>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('delete.admin', ['id' => $a->id]) }}" method="POST" style="display: inline-block;">
                                    <div class="btn-group" role="group" aria-label="Basic outlined example">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editData{{ $a->id }}"><i class="bi bi-pen-fill"></i></button>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="height: 33px;font-size: 13px;" onclick="showDeleteConfirmation(event, this)"><i class="bi bi-trash-fill"></i></button>
                                    </div>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editData{{ $a->id }}" tabindex="-1" role="dialog" aria-labelledby="importExcelLabel" aria-hidden="true" data-backdrop="false">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('edit.admin', $a->id) }}" method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header p-2 px-3" style="background-color: #012970;">
                                            <h5 class="modal-title text-center" id="importExcelLabel" style="color: #fff;">Edit Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body justify-content-center p-3 mb-0">
                                            @csrf
                                            <div class="registration-form">
                                                <div class="form-group">
                                                    <label for="nik">NIK</label>
                                                    <input type="text" class="form-control item mt-1" name="nik" id="nik" placeholder="Type here ..." value="{{ $a->nik }}">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label for="name">NAMA</label>
                                                    <input type="text" class="form-control item mt-1" name="name" id="name" placeholder="Type here ..." value="{{ $a->name }}">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label for="role">ROLE</label>
                                                    <select class="form-control item mt-1" name="role" id="role">
                                                        <option value="admin" {{ $a->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                        <option value="inspektur" {{ $a->role == 'inspektur' ? 'selected' : '' }}>Inspektur</option>
                                                    </select>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <label for="password">PASSWORD</label>
                                                    <div class="input-group d-flex" style="position: relative;">
                                                        <input type="password" class="form-control item mt-1" name="password" id="password" value="{{ $a->chain }}">
                                                        <i class="input-group-append bi bi-eye-slash-fill" style="z-index: 10;" onclick="togglePasswordVisibility(this)"></i>
                                                    </div>
                                                </div>
                                                <div class="form-group d-flex justify-content-center mt-3">
                                                    <button type="submit" class="btn btn-primary create-account">Simpan</button>
                                                    <button type="button" class="btn btn-secondary btn-cancel ms-2" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</main>
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    function showDeleteConfirmation(event, button) {
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
    function togglePasswordVisibility(icon) {
        var passwordInput = icon.previousElementSibling;
        var type = passwordInput.getAttribute('type');

        if (type === 'password') {
            passwordInput.setAttribute('type', 'text');
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        } else {
            passwordInput.setAttribute('type', 'password');
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        }
    }
</script>
<script>
    function filterData() {
        const selected = document.getElementById('searchTable').value;

        fetch(`{{ route('search.admin') }}?search=${selected}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('tableBody').innerHTML = data;
            });
    }

    document.getElementById('searchTable').addEventListener('input', function() {
        filterData();
    });
</script>
@endsection