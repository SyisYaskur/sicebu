@extends('layouts.teacher.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-2">Dashboard Wali Kelas</h4>

    <!-- Pesan Selamat Datang -->
    <div class="alert alert-success" role="alert">
        <i class="bx bx-check-circle me-2"></i>
        Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Anda adalah Wali Kelas untuk <strong>{{ $class->name }}</strong>.
    </div>

    <!-- REVISI 3: Pengingat Pemasukan Harian -->
    @if (!$hasIncomeToday)
    <div class="alert alert-danger" role="alert">
        <i class="bx bx-info-circle me-2"></i>
        <strong>PERHATIAN!</strong> Anda belum mencatat pemasukan kas harian untuk hari ini ({{ \Carbon\Carbon::today()->format('d M Y') }}).
        <a href="{{ route('walikelas.incomes.create') }}" class="alert-link">Klik di sini untuk mencatat.</a>
    </div>
    @endif

    <!-- REVISI 1 & 2: Kartu Statistik Sederhana -->
    <div class="row g-4 mb-4">
        <!-- Sisa Saldo (Paling Penting) -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Sisa Saldo Kas</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2 text-primary">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</h3>
                            </div>
                            <small>Total Pemasukan - Pengeluaran</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-wallet bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pemasukan Bulan Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Pemasukan (Bln Ini)</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2 text-success">+ Rp {{ number_format($stats['incomeThisMonth'], 0, ',', '.') }}</h4>
                            </div>
                            <small>Total pemasukan bulan ini</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pengeluaran Bulan Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Pengeluaran (Bln Ini)</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2 text-danger">- Rp {{ number_format($stats['expenseThisMonth'], 0, ',', '.') }}</h4>
                            </div>
                            <small>Total pengeluaran bulan ini</small>
                        </div>
                        <span class="badge bg-label-danger rounded p-2"><i class="bx bx-shopping-bag bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumlah Siswa -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Jumlah Siswa</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ $stats['studentCount'] }}</h4>
                            </div>
                            <small>Siswa di kelas {{ $class->name }}</small>
                        </div>
                        <span class="badge bg-label-info rounded p-2"><i class="bx bx-user bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REVISI 4: Tombol Aksi Cepat -->
    <div classrow g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Aksi Cepat</h5>
                    <p class="card-text">Pilih aksi yang ingin Anda lakukan:</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('walikelas.incomes.create') }}" class="btn btn-primary btn-lg">
                            <i class="bx bx-plus-circle me-1"></i>
                            Catat Pemasukan Harian
                        </a>
                        <a href="{{ route('walikelas.expenses.create') }}" class="btn btn-secondary btn-lg">
                            <i class="bx bx-minus-circle me-1"></i>
                            Catat Pengeluaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Kas Terbaru -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Aktivitas Kas Terbaru</h5>
            <a href="{{ route('walikelas.reports.index') }}" class="btn btn-sm btn-outline-primary">Lihat Laporan Lengkap</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($recentActivity as $activity)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($activity->date)->format('d M Y') }}</td>
                        <td>
                            @if ($activity->type == 'income')
                                Pemasukan Harian
                            @else
                                {{ $activity->description }}
                            @endif
                        </td>
                        <td>
                            @if ($activity->type == 'income')
                                <span class="text-success fw-medium">+ Rp {{ number_format($activity->amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-danger fw-medium">- Rp {{ number_format($activity->amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Belum ada aktivitas kas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection