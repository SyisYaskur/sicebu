@extends('layouts.admin.app')
@section('title', 'Pusat Laporan Keuangan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Laporan Keuangan Terpusat</h4>

    {{-- 1. FILTER COMPLEX --}}
    <div class="card mb-4">
        <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <div class="d-flex justify-content-between">
                <h5 class="mb-0"><i class="bx bx-filter-alt me-2"></i> Filter Data Lanjutan</h5>
                <i class="bx bx-chevron-down"></i>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form action="{{ route('superadmin.reports.index') }}" method="GET">
                    <div class="row g-3">
                        {{-- Baris 1 --}}
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kelas</label>
                            <select name="class_id" class="form-select select2">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="type" class="form-select">
                                <option value="">Semua (Masuk & Keluar)</option>
                                <option value="income" {{ $type == 'income' ? 'selected' : '' }}>Hanya Pemasukan</option>
                                <option value="expense" {{ $type == 'expense' ? 'selected' : '' }}>Hanya Pengeluaran</option>
                            </select>
                        </div>

                        {{-- Baris 2 --}}
                        <div class="col-md-3">
                            <label class="form-label">Pencatat (User)</label>
                            <select name="user_id" class="form-select select2">
                                <option value="">-- Semua User --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Min. Nominal (Rp)</label>
                            <input type="text" name="min_amount" class="form-control amount-input" value="{{ $minAmount }}" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max. Nominal (Rp)</label>
                            <input type="text" name="max_amount" class="form-control amount-input" value="{{ $maxAmount }}" placeholder="Unlimited">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" name="action" value="filter" class="btn btn-primary flex-grow-1">
                                <i class="bx bx-search"></i> Filter
                            </button>
                            <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="bx bx-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. STATISTIK DASHBOARD MINI --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <span class="d-block fw-semibold mb-1">Total Pemasukan</span>
                    <h3 class="card-title mb-0 text-success">Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <span class="d-block fw-semibold mb-1">Total Pengeluaran</span>
                    <h3 class="card-title mb-0 text-danger">Rp {{ number_format($stats['totalExpense'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <span class="d-block fw-semibold mb-1">Net Cash Flow</span>
                    <h3 class="card-title mb-0 {{ $stats['netCashFlow'] >= 0 ? 'text-primary' : 'text-warning' }}">
                        Rp {{ number_format($stats['netCashFlow'], 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <span class="d-block fw-semibold mb-1">Total Transaksi</span>
                    <h3 class="card-title mb-0 text-info">{{ $stats['transactionCount'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TABEL DATA & EXPORT --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Transaksi</h5>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bx bx-export me-1"></i> Export Data
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank"><i class="bx bxs-file-pdf me-2"></i> PDF (Cetak)</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"><i class="bx bxs-spreadsheet me-2"></i> Excel (Olah Data)</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th class="text-end">Nominal (Rp)</th>
                        <th>Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
                        <td>{{ $t->class_name }}</td>
                        <td>
                            @if($t->type == 'income')
                                <span class="badge bg-label-success">Masuk</span>
                            @else
                                <span class="badge bg-label-danger">Keluar</span>
                            @endif
                        </td>
                        <td style="max-width: 300px; white-space: normal;">
                            {{ $t->description }}
                            @if($t->type == 'expense' && $t->recipient)
                                <br><small class="text-muted">Ke: {{ $t->recipient }}</small>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $t->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td><small>{{ $t->pic_name }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">Tidak ada data ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination manual jika perlu, atau tampilkan semua untuk analisa --}}
        <div class="card-footer text-muted small">
            Menampilkan {{ $transactions->count() }} data sesuai filter.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() { $('.select2').select2({ theme: 'bootstrap-5', width: '100%' }); });
    
    document.querySelectorAll('.amount-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g, "");
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });
</script>
@endpush