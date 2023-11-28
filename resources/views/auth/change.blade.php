<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Ganti Sandi</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.all.js') }}"></script>

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
    @include('sweetalert::alert')

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <!-- <div class="d-flex justify-content-center py-4">
                                <a href="{{ route('login') }}" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">NiceAdmin</span>
                                </a>
                            </div> -->

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-2 pb-2">
                                        <h5 class="card-title text-center pb-0" style="font-size: 36px;">GANTI SANDI</h5>
                                        <p class="text-center small mb-1">Silahkan Masukkan Password Lama dan Password Baru untuk Mengganti Sandi</p>
                                    </div>

                                    <form class="row g-3 needs-validation" method="POST" action="{{ route('changePassword') }}" novalidate>
                                        @csrf
                                        <div class="col-12">
                                            <!-- <label for="password_lama" class="form-label">Password Lama</label> -->
                                            <div class="input-group has-validation">
                                                <!-- <span class="input-group-text" id="inputGroupPrepend">@</span> -->
                                                <input type="password" name="password_lama" class="form-control" id="password_lama" placeholder="Masukkan Password Lama" required>
                                                <div class="invalid-feedback">Password Lama harus diisi</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <!-- <label for="password_baru" class="form-label">Password Baru</label> -->
                                            <input type="password" name="password_baru" class="form-control" id="password_baru" placeholder="Masukkan Password Baru" required>
                                            <div class="invalid-feedback">Password Baru harus diisi</div>
                                        </div>

                                        <div class="col-12">
                                            <!-- <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label> -->
                                            <input type="password" name="password_confirm" class="form-control" id="password_confirm" placeholder="Konfirmasi Password Baru" required>
                                            <div class="invalid-feedback">Konfirmasi Password Baru harus diisi</div>
                                        </div>

                                        <!-- <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div> -->
                                        <div class="col-12 d-flex justify-content-center">
                                            <a href="{{ route('home') }}">
                                                <button type="button" class="btn btn-secondary me-1">CANCEL</button>
                                            </a>
                                            <button class="btn btn-primary ms-1" type="submit">GANTI</button>
                                        </div>
                                        <!-- <div class="col-12">
                                            <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p>
                                        </div> -->
                                    </form>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>