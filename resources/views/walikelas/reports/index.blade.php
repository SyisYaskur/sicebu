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
            <form action="{{ route('walikelas.reports.index') }}" method="GET" id="reportForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
                
                {{-- Opsi Tampilkan Grafik --}}
                <div class="mt-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show_chart" name="show_chart" value="1" {{ $showChart ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label" for="show_chart">Tampilkan Grafik Analisis</label>
                    </div>
                </div>
            </form>
            
            @if($stats)
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('walikelas.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-danger">
                            <i class="bx bxs-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($stats)
        
        {{-- 2. GRAFIK (Hanya Muncul Jika Checkbox Dicentang) --}}
        @if($showChart && $chartData)
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Tren Keuangan</h5>
                    <small class="text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</small>
                </div>
            </div>
            <div class="card-body px-0">
                <div id="financialChart" style="min-height: 350px;"></div>
            </div>
        </div>
        @endif

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
        
    @else
        <div class="alert alert-info text-center" role="alert">
            <i class="bx bx-calendar-edit fs-4 mb-2 d-block"></i>
            Silakan pilih rentang tanggal untuk melihat laporan.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

@if($showChart && $chartData)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartOptions = {
        series: [
            {
                name: 'Pemasukan',
                data: @json($chartData['series'][0]['data'])
            },
            {
                name: 'Pengeluaran',
                data: @json($chartData['series'][1]['data'])
            }
        ],
        chart: {
            height: 350,
            type: 'area', // Tipe Area Chart
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth', // Garis melengkung halus
            width: 2
        },
        xaxis: {
            categories: @json($chartData['categories']),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    // Format Angka Pendek (1k, 1M) atau Full
                    if (value >= 1000000) return (value / 1000000).toFixed(1) + "Jt";
                    if (value >= 1000) return (value / 1000).toFixed(0) + "rb";
                    return value;
                }
            },
        },
        fill: {
            type: 'gradient', // Efek Gradasi (Seperti Gambar)
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2, // Memudar ke bawah
                stops: [0, 90, 100]
            }
        },
        colors: ['#00E396', '#FF4560'], // Hijau Cerah (Masuk) & Merah Cerah (Keluar)
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
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        }
    };

    const chart = new ApexCharts(document.querySelector("#financialChart"), chartOptions);
    chart.render();
});
</script>
@endif
@endpush