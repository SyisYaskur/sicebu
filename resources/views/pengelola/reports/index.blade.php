@extends('layouts.pengelola.app')
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
                <form action="{{ route('pengelola.reports.index') }}" method="GET">
                    <div class="row g-3">

                        {{-- Baris 1 --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="class_id" class="form-select select2">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                                        {{ $c->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Jenis Transaksi</label>
                            <select name="type" class="form-select">
                                <option value="">Semua (Masuk & Keluar)</option>
                                <option value="income" {{ $type == 'income' ? 'selected' : '' }}>Hanya Pemasukan</option>
                                <option value="expense" {{ $type == 'expense' ? 'selected' : '' }}>Hanya Pengeluaran</option>
                            </select>
                        </div>

                        {{-- Baris 2 --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Pencatat (User)</label>
                            <select name="user_id" class="form-select select2">
                                <option value="">-- Semua User --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Min. Nominal (Rp)</label>
                            <input type="text" name="min_amount" class="form-control amount-input"
                                value="{{ $minAmount }}" placeholder="0">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Max. Nominal (Rp)</label>
                            <input type="text" name="max_amount" class="form-control amount-input"
                                value="{{ $maxAmount }}" placeholder="Unlimited">
                        </div>

                        {{-- Baris 3: Toggle + Tombol --}}
                        <div class="col-md-12 d-flex flex-wrap align-items-center justify-content-between mt-2">

                            {{-- Toggle Grafik --}}
                            <div class="form-check form-switch my-2">
                                <input class="form-check-input" type="checkbox" id="show_chart" name="show_chart"
                                    value="1" {{ $showChart ? 'checked' : '' }} onchange="this.form.submit()">

                                <label class="form-check-label fw-bold text-primary" for="show_chart">
                                    <i class="bx bx-bar-chart-alt-2 me-1"></i>
                                    Tampilkan Grafik Analisis Visual
                                </label>
                            </div>

                            {{-- Tombol Filter + Reset --}}
                            <div class="d-flex gap-2 my-2">
                                <button type="submit" name="action" value="filter" class="btn btn-primary px-4">
                                    <i class="bx bx-search"></i> Filter
                                </button>

                                <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline-secondary" title="Reset">
                                    <i class="bx bx-refresh"></i>
                                </a>
                            </div>
                        </div>  
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- GRAFIK ANALISIS --}}
    @if($showChart && $chartData)
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Analisis Tren Keuangan</h5>
            <span class="badge bg-label-primary">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        </div>
        <div class="card-body">
            <div id="financialChart" style="min-height: 350px;"></div>
        </div>
    </div>
    @endif

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
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    $(document).ready(function() { $('.select2').select2({ theme: 'bootstrap-5', width: '100%' }); });
    
    document.querySelectorAll('.amount-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g, "");
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });
</script>
@if($showChart && $chartData)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartOptions = {
        series: @json($chartData['series']), // Data dinamis dari controller
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: @json($chartData['categories']), // Tanggal
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    // Format K (Ribu) / M (Juta)
                    if (value >= 1000000) return (value / 1000000).toFixed(1) + "Jt";
                    if (value >= 1000) return (value / 1000).toFixed(0) + "rb";
                    return value;
                }
            },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        // Warna: Hijau (Pemasukan), Merah (Pengeluaran)
        // Urutannya tergantung series mana yang dikirim controller.
        // Karena controller mengirim Income dulu lalu Expense (jika ada dua-duanya),
        // maka array warna harus [Hijau, Merah].
        colors: ['#00E396', '#FF4560'], 
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + val.toLocaleString('id-ID');
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        legend: { position: 'top', horizontalAlign: 'right' }
    };

    const chart = new ApexCharts(document.querySelector("#financialChart"), chartOptions);
    chart.render();
});
</script>
@endif
@endpush