@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<main id="main" class="main">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
            <input type="text" name="query" id="searchHome" placeholder="Search" title="Enter search keyword">
            <button type="button" title="Search"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <!-- <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav> -->
    </div>

    <section class="section home dashboard">
        <h5 class="card-title pt-3" style="font-weight: 500;">Recently Added</h5>
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    @php
                    $icons = ['bi bi-puzzle', 'bi bi-gear-wide-connected', 'bi bi-box-seam', 'bi bi-archive'];
                    $color = ['sales-card', 'revenue-card', 'customers-card', 'product-card'];
                    $iconIndex = 0;
                    @endphp
                    @foreach($latest as $last)
                    <div class="col-xxl-4 col-md-6 col-6">
                        <a href="{{ Auth::user()->role == 'admin' ? route('fasilitas', $last->tabel) : route('fasilitas.guest', $last->tabel) }}" class="card-link">
                            <div class="card info-card {{ $color[$iconIndex] }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ strtoupper(str_replace('_', ' ', $last->tabel)) }} <span><br>| {{ $last->created_at->shortRelativeDiffForHumans() }}</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="{{ $icons[$iconIndex] }}"></i>
                                        </div>
                                        <div class="ps-3">
                                            <span style="font-size: 10px;color: #899bbd;">TOTAL DATA:</span>
                                            <h6>{{ $tableCounts[$last->tabel] }}</h6>
                                            @if(array_key_exists($last->tabel, $totalFalse))
                                            <span class="text-danger small pt-1 fw-bold">NOT OK</span> <span class="text-muted small pt-2 ps-1">{{ $totalFalse[$last->tabel] }}</span>
                                            @else
                                            <span class="text-danger small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </a>
                    </div>
                    @php
                    $iconIndex = ($iconIndex + 1) % count($icons);
                    @endphp
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Activity <span>| Today</span></h5>

                        <div class="activity">
                            @foreach($activity as $act)
                            <div class="activity-item d-flex">
                                <div class="activite-label">{{ $act->created_at->shortRelativeDiffForHumans() }}</div>
                                <i class='bi bi-circle-fill activity-badge {{ $act->status }} align-self-start'></i>
                                <div class="activity-content"><a href="#" class="fw-bold text-dark">
                                        {{ $act->user }} </a>{{ $act->description }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection