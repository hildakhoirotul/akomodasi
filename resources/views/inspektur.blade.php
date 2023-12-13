@extends('layouts.master')

@section('content')
<section class="section inspektur dashboard">
    <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                <!-- Sales Card -->
                @php
                $icons = ['bi bi-puzzle', 'bi bi-gear-wide-connected', 'bi bi-box-seam', 'bi bi-archive'];
                $color = ['sales-card', 'revenue-card', 'customers-card', 'product-card'];
                $iconIndex = 0;
                @endphp
                @foreach($list as $l)
                <div class="col-xxl-4 col-md-3 col-6">
                    <a href="{{ route('properti.inspektur', ['nama_tabel' => str_replace(' ', '_', strtolower($l->name))]) }}">
                        <div class="card info-card {{ $color[$iconIndex] }}">
                            <div class="card-body inspektur-card text-center">

                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="{{ $icons[$iconIndex] }}"></i>
                                    </div>
                                </div>
                                <h5 class="card-title">{{ $l->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
                @php
                $iconIndex = ($iconIndex + 1) % count($icons);
                @endphp
                @endforeach<!-- End Sales Card -->
            </div>
        </div><!-- End Left side columns -->
    </div>
</section>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
@endsection