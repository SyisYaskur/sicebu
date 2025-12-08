@extends('layouts.admin.app')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Super Admin</h4>

    {{-- 1. KARTU STATISTIK (Sama) --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Pengguna</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2">{{ $stats['total_users'] }}</h3></div>
                            <small>Guru, Admin, Pengelola</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-user bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Siswa</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2">{{ $stats['total_students'] }}</h3></div>
                            <small>Terdata di sistem</small>
                        </div>
                        <span class="badge bg-label-info rounded p-2"><i class="bx bx-group bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Kelas</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2">{{ $stats['total_classes'] }}</h3></div>
                            <small>Kelas Aktif</small>
                        </div>
                        <span class="badge bg-label-warning rounded p-2"><i class="bx bx-chalkboard bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Wali Kelas Aktif</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2">{{ $stats['active_homerooms'] }}</h3></div>
                            <small>Dari {{ $stats['total_classes'] }} kelas</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2"><i class="bx bx-id-card bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 2. GRAFIK TREN KEUANGAN (UPDATE) --}}
        <div class="col-md-6 col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Tren Keuangan (7 Hari Terakhir)</h5>
                    <small class="text-muted">Nominal & Frekuensi</small>
                </div>
                <div class="card-body">
                    <div id="transactionChart"></div>
                </div>
            </div>
        </div>

        {{-- 3. GRAFIK DISTRIBUSI KELAS (Sama) --}}
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Distribusi Kelas</h5>
                </div>
                <div class="card-body">
                    <div id="classDistributionChart"></div>
                    <div class="mt-3 text-center small text-muted">
                        Berdasarkan Tingkat (10, 11, 12)
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABEL TRANSAKSI TERBARU (UPDATE) --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transaksi Terbaru</h5>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Lihat Semua
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('superadmin.incomes.index') }}">Pemasukan</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.expenses.index') }}">Pengeluaran</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Keterangan</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center">Tipe</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($recentTransactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                        <td>{{ $transaction->classRoom->full_name ?? '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($transaction->description, 30) }}</td>
                        <td class="text-end fw-bold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($transaction->type == 'income')
                                <span class="badge bg-label-success">Masuk</span>
                            @else
                                <span class="badge bg-label-danger">Keluar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. CHART TRANSAKSI (Area Chart dengan Custom Tooltip)
    // Data Nominal
    const incomeAmount = @json($barChartData['incomes_amount']);
    const expenseAmount = @json($barChartData['expenses_amount']);
    
    // Data Frekuensi (untuk Tooltip)
    const incomeCount = @json($barChartData['incomes_count']);
    const expenseCount = @json($barChartData['expenses_count']);

    const transactionOptions = {
        series: [{
            name: 'Pemasukan',
            data: incomeAmount
        }, {
            name: 'Pengeluaran',
            data: expenseAmount
        }],
        chart: {
            height: 350,
            type: 'area', // Area Chart agar terlihat modern
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: @json($barChartData['categories']),
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    if (val >= 1000000) return (val / 1000000).toFixed(1) + "Jt";
                    if (val >= 1000) return (val / 1000).toFixed(0) + "rb";
                    return val;
                }
            }
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
        colors: ['#00E396', '#FF4560'], // Hijau & Merah
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (y, { seriesIndex, dataPointIndex, w }) {
                    // Logic Custom Tooltip: Tampilkan Rupiah DAN Frekuensi
                    let count = 0;
                    if(seriesIndex === 0) { // Pemasukan
                        count = incomeCount[dataPointIndex];
                    } else { // Pengeluaran
                        count = expenseCount[dataPointIndex];
                    }
                    return "Rp " + y.toLocaleString('id-ID') + " (" + count + " Transaksi)";
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#transactionChart"), transactionOptions).render();

    // 2. Pie Chart (Distribusi) - Biarkan Sama
    const pieOptions = {
        series: @json($pieChartData['series']),
        labels: @json($pieChartData['labels']),
        chart: { type: 'donut', height: 350 },
        colors: ['#696cff', '#71dd37', '#03c3ec'],
        legend: { position: 'bottom' },
        plotOptions: {
            pie: { donut: { labels: { show: true, total: { show: true, label: 'Total Kelas', formatter: function (w) { return w.globals.seriesTotals.reduce((a, b) => a + b, 0) } } } } }
        }
    };
    new ApexCharts(document.querySelector("#classDistributionChart"), pieOptions).render();
});
</script>
@endpush