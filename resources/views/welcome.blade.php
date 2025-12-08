<!DOCTYPE html>
<html lang="id" class="light-style layout-navbar-fixed layout-wide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="front-pages">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>SICEBU - SMKN 1 Talaga</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    
    <style>
        body {
            background-color: #f5f5f9; /* Warna background abu muda Sneat */
        }
        .hero-header {
            background: linear-gradient(135deg, #696cff 0%, #4346d3 100%); /* Gradasi Ungu Sneat */
            padding: 4rem 0 8rem 0;
            color: white;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            margin-bottom: -50px; /* Agar konten bawahnya naik sedikit */
        }
        .logo-container {
            background: white;
            padding: 15px;
            border-radius: 50%; /* Membuat lingkaran */
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            display: inline-block;
            margin-bottom: 20px;
        }
        .report-card {
            margin-top: -80px; /* Menumpuk di atas hero section */
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .navbar-transparent {
            background: transparent !important;
            box-shadow: none !important;
        }
        .navbar-brand {
            color: white !important;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
        }
        .nav-link:hover {
            color: white !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-transparent position-absolute w-100 zindex-5">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <i class="bx bxs-book-content fs-3"></i> SICEBU
            </a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm fw-bold text-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-white btn-sm text-white border-white">Login Petugas</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <header class="hero-header text-center">
        <div class="container">
            <div class="logo-container">
                <img src="data:image/png;base64,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" 
                     alt="Logo SMKN 1 Talaga" width="70">
            </div>

            <h1 class="display-5 fw-bold text-white mb-2">SICEBU SMKN 1 Talaga</h1>
            <p class="lead mb-4 opacity-75 mx-auto" style="max-width: 700px;">
                Sistem Catatan Buku Keuangan Kelas yang Transparan, Akuntabel, dan Terintegrasi.
                Memudahkan pengelolaan dana kelas untuk kemajuan bersama.
            </p>
        </div>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card report-card p-4">
                    <h4 class="text-center mb-4 text-primary fw-bold">Cek Laporan Publik</h4>
                    
                    <form action="{{ route('welcome') }}" method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label fw-bold">Pilih Kelas</label>
                                <select name="class_id" class="form-select form-select-lg">
                                    <option value="" disabled {{ !$selectedClassId ? 'selected' : '' }}>-- Cari Kelas --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                            {{ $class->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Periode</label>
                                <input type="month" name="month" class="form-control form-control-lg" value="{{ $selectedMonth }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bx bx-search me-1"></i> Tampilkan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @if($reportData)
                <div class="card mt-4 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 text-primary fw-bold">{{ $reportData['class_name'] }}</h5>
                                <span class="badge bg-label-secondary">Periode: {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Sisa Saldo Kas</small>
                                <h3 class="text-success mb-0 fw-bold">Rp {{ number_format($reportData['balance'], 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-4">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData['transactions'] as $transaction)
                                <tr>
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($transaction->type == 'income')
                                            <span class="badge bg-label-success me-2"><i class="bx bx-down-arrow-alt"></i> Masuk</span>
                                            Pemasukan Harian
                                        @else
                                            <span class="badge bg-label-danger me-2"><i class="bx bx-up-arrow-alt"></i> Keluar</span>
                                            {{ $transaction->description }}
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 fw-bold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }} 
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <img src="{{ asset('assets/img/illustrations/girl-doing-yoga-light.png') }}" width="150" class="mb-3">
                                        <p>Belum ada data transaksi pada periode ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="container text-center py-5 mt-5">
        <p class="text-muted mb-0">© {{ date('Y') }} SICEBU SMKN 1 Talaga. All rights reserved.</p>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>