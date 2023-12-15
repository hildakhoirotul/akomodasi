@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
<section class="section inspektur dashboard">
    <div class="search-bar">
        <div class="search-form d-flex align-items-center">
            <input type="text" name="query" id="searchTable" placeholder="Search" oninput="searchProperti()" title="Enter search keyword">
            <button type="button" title="Search"><i class="bi bi-search"></i></button>
        </div>
    </div>
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
                <div class="col-xxl-4 col-md-3 col-6 searchable">
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

<script>
    function searchProperti() {
        var searchInput = document.getElementById("searchTable").value.toLowerCase();
        var searchableElements = document.querySelectorAll(".searchable");

        searchableElements.forEach(function(element) {
            var text = element.textContent.toLowerCase();

            if (text.includes(searchInput)) {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        });
    }
</script>
@endsection