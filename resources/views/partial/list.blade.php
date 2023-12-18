@php $i=1 @endphp
@foreach($result as $l)
<tr>
    <th>{{ $i++ }}</th>
    <td><a href="{{ Auth::user()->role == 'admin' ? route('fasilitas', ['nama_tabel' => str_replace(' ', '_', strtolower($l->name))]) : route('fasilitas.guest', ['nama_tabel' => str_replace(' ', '_', strtolower($l->name))]) }}">{{ $l->name }}</a></td>
    <td>
        @if ($l->columns)
        {{ implode(', ', json_decode($l->columns, true)) }}
        @else
        0
        @endif
    </td>
    <td>{{ $l->jumlah_atribut }}</td>
    <td>{{ $l->jumlah }}</td>
    @if(Auth::user()->role == 'admin')
    <td>
        <form action="{{ route('delete.table', ['tabel' => $l->name]) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger" style="height: 33px;font-size: 13px;" onclick="confirmDelete(this)"><i class="bi bi-trash-fill"></i></button>
        </form>
    </td>
    @endif
</tr>
@endforeach