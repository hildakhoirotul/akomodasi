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
    @if(Auth::user()->is_admin)
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