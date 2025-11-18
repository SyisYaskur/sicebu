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
        .hero-section {
            background: linear-gradient(135deg, #696cff 0%, #4346d3 100%);
            color: white;
            padding: 100px 0 60px;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
        }
        .stats-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .navbar-landing {
            background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-landing">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="#">
                <i class="bx bxs-book-content fs-3"></i> SICEBU
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#statistik">Statistik</a></li>
                    <li class="nav-item"><a class="nav-link" href="#cek-data">Cek Laporan</a></li>
                    <li class="nav-item ms-lg-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm px-4">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4">Login Petugas</a>
                            @endauth
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="beranda" class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3 text-white">SICEBU SMKN 1 Talaga</h1>
                    <p class="lead mb-4 opacity-75">
                        Sistem Catatan Buku Keuangan Kelas yang Transparan, Akuntabel, dan Terintegrasi.
                        Memudahkan pengelolaan dana kelas untuk kemajuan bersama.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#cek-data" class="btn btn-light btn-lg fw-semibold text-primary">
                            <i class="bx bx-search-alt me-2"></i> Cek Laporan Publik
                        </a>
                    </div>
                </div>
            </div>
            <img src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/assets/img/illustrations/man-with-laptop-light.png" 
                 alt="Hero Image" class="img-fluid mt-5" style="max-height: 300px;">
        </div>
    </section>

    <section id="statistik" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-primary">Transparansi Data Sekolah</h2>
                <p class="text-muted">Rekapitulasi dana dari seluruh kelas secara real-time.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card stats-card h-100 border-primary border-top border-3">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-wallet fs-3"></i></span>
                            </div>
                            <h5 class="card-title mb-1">Total Saldo Sekolah</h5>
                            <h3 class="text-primary fw-bold mb-0">Rp {{ number_format($globalStats['total_balance'], 0, ',', '.') }}</h3>
                            <small class="text-muted">Akumulasi seluruh kelas</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stats-card h-100 border-success border-top border-3">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-trending-up fs-3"></i></span>
                            </div>
                            <h5 class="card-title mb-1">Pemasukan Bulan Ini</h5>
                            <h3 class="text-success fw-bold mb-0">+ Rp {{ number_format($globalStats['income_this_month'], 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card stats-card h-100 border-danger border-top border-3">
                        <div class="card-body text-center">
                            <div class="avatar mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-trending-down fs-3"></i></span>
                            </div>
                            <h5 class="card-title mb-1">Pengeluaran Bulan Ini</h5>
                            <h3 class="text-danger fw-bold mb-0">- Rp {{ number_format($globalStats['expense_this_month'], 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="cek-data" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Cek Laporan Kelas</h2>
                <p class="text-muted">Pilih kelas dan periode untuk melihat aktivitas keuangan.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('welcome') }}#cek-data" method="GET" class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label for="class_id" class="form-label fw-semibold">Pilih Kelas</label>
                                    <select name="class_id" id="class_id" class="form-select form-select-lg" required>
                                        <option value="" disabled {{ !$selectedClassId ? 'selected' : '' }}>-- Silakan Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="month" class="form-label fw-semibold">Periode (Bulan/Tahun)</label>
                                    <input type="month" class="form-control form-control-lg" name="month" id="month" value="{{ $selectedMonth }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bx bx-search me-1"></i> Tampilkan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($reportData)
                    <div class="card shadow-sm animate__animated animate__fadeInUp">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h4 class="mb-1 text-primary">Laporan Keuangan: {{ $reportData['class_name'] }}</h4>
                                    <span class="badge bg-label-secondary">{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Sisa Saldo Kelas</small>
                                    <h3 class="text-primary mb-0">Rp {{ number_format($reportData['balance'], 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Jenis</th>
                                        <th class="text-end pe-4">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['transactions'] as $transaction)
                                    <tr>
                                        <td class="ps-4">{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td>
                                            @if($transaction->type == 'income')
                                                <span class="badge bg-label-success">Pemasukan</span>
                                            @else
                                                <span class="badge bg-label-danger">Pengeluaran</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 fw-semibold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }} 
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                            Belum ada data transaksi pada periode ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @elseif($selectedClassId)
                     <div class="alert alert-warning text-center">
                        Data kelas tidak ditemukan.
                     </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <h5 class="text-white mb-1">SICEBU SMKN 1 Talaga</h5>
                    <small class="text-white-50">© {{ date('Y') }} Sistem Catatan Buku. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 me-3 text-decoration-none">Tentang Kami</a>
                    <a href="#" class="text-white-50 me-3 text-decoration-none">Kontak</a>
                    <a href="#" class="text-white-50 text-decoration-none">Bantuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>