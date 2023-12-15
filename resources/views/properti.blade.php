@extends('layouts.master')
@section('title', $fasilitas)

@section('content')
<section class="input-properti">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-xl-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title py-3">{{ $fasilitas }}</h4>
                        <a href="{{ route('home.inspektur') }}" class="badge bg-primary px-2" style="font-size: 16px;"><i class="bi bi-house"></i></a>
                    </div>
                    <!-- Floating Labels Form -->
                    <form class="row g-3" action="{{ route('store.properti', $nama_tabel)}}" method="POST">
                        @csrf
                        @foreach($columns as $column)
                        @if ($column !== 'id' && $column !== 'created_at' && $column !== 'updated_at')
                        <div class="col-md-12">
                            <div class="form-floating">
                                @if ($columnTypes[$column] === 'date')
                                <input type="date" class="form-control" id="{{ $column }}" name="{{ $column }}" placeholder="{{ ucfirst($column) }}">
                                <label for="{{ $column }}">{{ ucfirst($column) }}</label>
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
                                <input type="time" class="form-control" id="{{ $column }}" name="{{ $column }}" placeholder="{{ ucfirst($column) }}">
                                <label for="{{ $column }}">{{ ucfirst($column) }}</label>
                                @elseif ($columnTypes[$column] === 'integer')
                                <input type="number" class="form-control" id="{{ $column }}" name="{{ $column }}" placeholder="{{ ucfirst($column) }}">
                                <label for="{{ $column }}">{{ ucfirst($column) }}</label>
                                @elseif ($columnTypes[$column] === 'text')
                                <textarea class="form-control" placeholder="{{ ucfirst($column) }}" id="{{ $column }}" name="{{ $column }}" style="height: 100px;"></textarea>
                                <label for="{{ $column }}">{{ ucfirst($column) }}</label>
                                @else
                                <input type="text" class="form-control" id="{{ $column }}" name="{{ $column }}" placeholder="{{ ucfirst($column) }}">
                                <label for="{{ $column }}">{{ ucfirst($column) }}</label>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-secondary">Reset</button>
                        </div>
                    </form><!-- End floating Labels Form -->

                </div>
            </div>
        </div>
    </div>
</section>
@endsection