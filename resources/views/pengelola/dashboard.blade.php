@extends('layouts.pengelola.app')

@section('title', 'Dashboard Pengelola')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- 1. STATISTIK UTAMA --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-wallet"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Saldo Sekolah</span>
                    <h3 class="card-title mb-2 text-primary">Rp {{ number_format($stats['totalBalance'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-trending-up"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pemasukan (Bln Ini)</span>
                    <h3 class="card-title mb-2 text-success">+ Rp {{ number_format($stats['monthlyIncome'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-trending-down"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pengeluaran (Bln Ini)</span>
                    <h3 class="card-title mb-2 text-danger">- Rp {{ number_format($stats['monthlyExpense'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-transfer"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Penyaluran (Bln Ini)</span>
                    <h3 class="card-title mb-2 text-warning">Rp {{ number_format($stats['monthlyDisbursement'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 2. GRAFIK TREN KEUANGAN --}}
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Tren Keuangan Sekolah (12 Bulan Terakhir)</h5>
                </div>
                <div class="card-body">
                    <div id="financialChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 3. LEADERBOARD (TOP 5 SALDO) --}}
        <div class="col-md-6 col-lg-4 order-1 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2"><i class="bx bx-trophy text-warning me-2"></i>Top 5 Saldo Tertinggi</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($topClasses as $class)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">{{ substr($class->name, 0, 2) }}</span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $class->full_name }}</h6>
                                    <small class="text-muted">{{ $class->teacher_name ?? '-' }}</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold text-primary">Rp {{ number_format($class->balance, 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3. LEADERBOARD (BOTTOM 5 SALDO) --}}
        <div class="col-md-6 col-lg-4 order-2 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2"><i class="bx bx-trending-down text-danger me-2"></i>5 Saldo Terendah</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($lowClasses as $class)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary">{{ substr($class->name, 0, 2) }}</span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $class->full_name }}</h6>
                                    <small class="text-muted">{{ $class->teacher_name ?? '-' }}</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold text-danger">Rp {{ number_format($class->balance, 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- 4. PENYALURAN TERAKHIR --}}
        <div class="col-md-12 col-lg-4 order-3 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Penyaluran Terakhir</h5>
                    <a href="{{ route('pengelola.disbursements.index') }}" class="small">Lihat Semua</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-borderless">
                        <tbody>
                            @foreach($recentDisbursements as $d)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-paper-plane"></i></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $d->purpose }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($d->disbursement_date)->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @endforeach
                            @if($recentDisbursements->isEmpty())
                            <tr><td colspan="2" class="text-center text-muted small">Belum ada data.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data dari Controller
    const chartLabels = @json($chartData['labels']);
    const chartIncomes = @json($chartData['incomes']);
    const chartExpenses = @json($chartData['expenses']);

    // Konfigurasi ApexCharts
    const options = {
        series: [{
            name: 'Pemasukan',
            data: chartIncomes
        }, {
            name: 'Pengeluaran',
            data: chartExpenses
        }],
        chart: {
            height: 350,
            type: 'area', // Bisa diganti 'bar' atau 'line'
            toolbar: { show: false }
        },
        colors: ['#71dd37', '#ff3e1d'], // Hijau (Masuk), Merah (Keluar)
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
            categories: chartLabels,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + val.toLocaleString('id-ID')
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#financialChart"), options);
    chart.render();
});
</script>
@endpush