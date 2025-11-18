@extends('layouts.teacher.app')
@section('title', 'Laporan Kelas ' . $class->name)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kelas Saya /</span> Laporan Keuangan</h4>
    @include('components.alert')

    {{-- 1. FORM FILTER --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Laporan</h5>
        </div>
        <div class="card-body">
            {{-- Form menggunakan method GET agar filter menempel di URL --}}
            <form action="{{ route('walikelas.reports.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        {{-- Ambil nilai lama dari request --}}
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
            
            {{-- Tombol Cetak PDF hanya muncul jika filter aktif --}}
            @if($stats)
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        {{-- request()->query() akan mengambil start_date & end_date dari URL saat ini --}}
                        <a href="{{ route('walikelas.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-success">
                            <i class="bx bxs-file-pdf"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 2. HASIL LAPORAN --}}
    @if($stats) {{-- Tampilkan hanya jika $stats (hasil query) ada --}}
        
        {{-- Kartu Ringkasan --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body"><span class="badge bg-label-success rounded p-2 float-end"><i class="bx bx-dollar bx-sm"></i></span>
                        <span>Total Pemasukan</span>
                        <h3 class="mb-0 me-2 text-success">+ Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body"><span class="badge bg-label-danger rounded p-2 float-end"><i class="bx bx-shopping-bag bx-sm"></i></span>
                        <span>Total Pengeluaran</span>
                        <h3 class="mb-0 me-2 text-danger">- Rp {{ number_format($stats['totalExpense'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body"><span class="badge bg-label-primary rounded p-2 float-end"><i class="bx bx-wallet bx-sm"></i></span>
                        <span>Sisa Saldo</span>
                        <h3 class="mb-0 me-2 text-primary">= Rp {{ number_format($stats['balance'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rincian --}}
        <div class="row">
            <!-- Rincian Pemasukan -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Rincian Pemasukan</h5></div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Tanggal</th><th>Jumlah (Rp)</th><th>Keterangan</th><th>Dicatat Oleh</th></tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($incomes as $income)
                                <tr>
                                    <td>{{ $income->date->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                    <td>{{ $income->description ?? '-' }}</td>
                                    <td>{{ $income->creator->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">Tidak ada pemasukan pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Rincian Pengeluaran -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Rincian Pengeluaran</h5></div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Tanggal</th><th>Jumlah (Rp)</th><th>Keterangan</th><th>Penerima</th><th>Dicatat Oleh</th></tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ $expense->recipient ?? '-' }}</td>
                                    <td>{{ $expense->creator->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">Tidak ada pengeluaran pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    @else {{-- Tampilkan jika $stats masih null (belum filter) --}}
        <div class="alert alert-info" role="alert">
            <i class="bx bx-info-circle me-2"></i>
            Silakan pilih rentang tanggal (Tanggal Mulai dan Tanggal Akhir) lalu klik "Tampilkan" untuk melihat laporan keuangan.
        </div>
    @endif
</div>
@endsection