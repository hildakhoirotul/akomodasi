@php $i=1 @endphp
@foreach($data as $a)
<tr>
    <th>{{ $i++ }}</th>
    <td>{{ $a->nik }}</td>
    <td>{{ $a->name }}</td>
    <td>
        <div class="password-container">
            <input type="password" class="password-text" data-chain="{{ $a->chain }}" value="{{ $a->chain }}" readonly>
            <i class="toggle-password-icon bi bi-eye-slash-fill" onclick="togglePasswordVisibility(this)"></i>
        </div>
    </td>
    <td>
        <form action="{{ route('delete.admin', ['id' => $a->id]) }}" method="POST" style="display: inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="height: 33px;font-size: 13px;" onclick="showDeleteConfirmation(event, this)"><i class="bi bi-trash-fill"></i></button>
        </form>
    </td>
</tr>
@endforeach