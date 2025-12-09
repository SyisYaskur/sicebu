<!DOCTYPE html>
<html lang="id" class="light-style layout-navbar-fixed layout-wide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="front-pages">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>SICEBU - SMKN 1 Talaga</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    
    <style>
        body { background-color: #f5f5f9; }
        .hero-mini { background: #fff; padding: 2rem 0 1rem; border-bottom: 1px solid #e0e0e0; text-align: center; }
        .balance-card-small {
            background: linear-gradient(135deg, #696cff 0%, #4346d3 100%);
            color: white; border-radius: 0.5rem; padding: 1.5rem; text-align: center;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="#">
                <i class="bx bxs-book-content fs-3"></i> SICEBU
            </a>
            <div class="ms-auto">
                @auth <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login Petugas</a> @endauth
            </div>
        </div>
    </nav>

    <section class="hero-mini">
        <div class="container">
            <h3 class="fw-bold text-dark mb-2">SICEBU</h3>
            <p class="text-muted mb-0 small mx-auto" style="max-width: 600px;">
                Pantau tren pemasukan dan pengeluaran dana kelas secara real-time.
            </p>
        </div>
    </section>

    <div class="container py-4">
        
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 fw-bold">Tren Keuangan Sekolah</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active btn-sm" onclick="updateChart('7days', this)">7 Hari</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="updateChart('30days', this)">1 Bulan</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="updateChart('1year', this)">1 Tahun</button>
                </div>
            </div>
            <div class="card-body px-2 pb-0">
                {{-- REVISI: Height diset kecil (250px) --}}
                <div id="financialChart" style="min-height: 250px;"></div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-label-success py-2"><h6 class="mb-0 fw-bold text-success"><i class="bx bx-trending-up me-2"></i>3 Kelas Saldo Terbanyak</h6></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                @foreach($top3Classes as $cls)
                                <tr>
                                    <td class="ps-3 small fw-bold text-muted">#{{ $loop->iteration }}</td>
                                    <td class="small">{{ $cls->full_name }}</td>
                                    <td class="text-end pe-3 small fw-bold text-success">Rp {{ number_format($cls->current_balance, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-label-warning py-2"><h6 class="mb-0 fw-bold text-warning"><i class="bx bx-trending-down me-2"></i>3 Kelas Saldo Terkecil</h6></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <tbody>
                                @foreach($low3Classes as $cls)
                                <tr>
                                    <td class="ps-3 small fw-bold text-muted">#{{ $loop->iteration }}</td>
                                    <td class="small">{{ $cls->full_name }}</td>
                                    <td class="text-end pe-3 small fw-bold text-warning">Rp {{ number_format($cls->current_balance, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="laporan">
            <div class="col-lg-8">
                <div class="card mb-4 shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-filter-alt me-2"></i> Filter Laporan Detail</h6>
                    </div>
                    <div class="card-body pt-3">
                        <form action="{{ route('welcome') }}#laporan" method="GET">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted mb-1">Kelas</label>
                                    <select name="class_id" class="form-select form-select-sm">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted mb-1">Mulai</label>
                                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted mb-1">Sampai</label>
                                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan Laporan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="balance-card-small h-100 d-flex flex-column justify-content-center shadow-sm">
                    <i class="bx bxs-bank fs-2 mb-2 opacity-50"></i>
                    <small class="text-white-50 text-uppercase fw-bold mb-0" style="font-size: 0.7rem;">Total Saldo Sekolah</small>
                    <h3 class="text-white mb-0 fw-bold">Rp {{ number_format($globalBalance, 0, ',', '.') }}</h3>
                    <small class="text-white-50 mt-1" style="font-size: 0.7rem;">
                        Per {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }} <br>
                        {{ $classId ? '(Khusus Kelas Terpilih)' : '(Seluruh Sekolah)' }}
                    </small>
                </div>
            </div>
        </div>

        <div class="card mt-4 shadow-sm border-0">
            <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-white small">Rincian Transaksi</h6>
                <span class="badge bg-label-primary">{{ $classId ? \App\Models\RefClass::find($classId)->full_name : 'Semua Kelas' }}</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped mb-0 table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            @if(!$classId) <th>Kelas</th> @endif
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedData as $index => $row)
                            @if(isset($row->is_header) && $row->is_header)
                                <tr class="table-warning fw-bold fst-italic small">
                                    <td class="text-center">-</td>
                                    @if(!$classId) <td>-</td> @endif
                                    <td>{{ $row->date->format('d/m/Y') }}</td>
                                    <td>{{ $row->description }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end text-primary">{{ number_format($row->balance, 0, ',', '.') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-center small">{{ $paginatedData->firstItem() + $index }}</td>
                                    @if(!$classId) <td><span class="badge bg-label-secondary text-dark small">{{ $row->class_full_name }}</span></td> @endif
                                    <td class="small">{{ $row->date->format('d/m/Y') }}</td>
                                    <td class="small">{{ Str::limit($row->description, 40) }}</td>
                                    <td class="text-end text-success fw-semibold small">{{ $row->income > 0 ? number_format($row->income, 0, ',', '.') : '-' }}</td>
                                    <td class="text-end text-danger fw-semibold small">{{ $row->expense > 0 ? number_format($row->expense, 0, ',', '.') : '-' }}</td>
                                    <td class="text-end fw-bold text-primary small">{{ number_format($row->balance, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="{{ $classId ? 6 : 7 }}" class="text-center py-4 text-muted small">Tidak ada data transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-2">{{ $paginatedData->appends(request()->query())->links() }}</div>
        </div>
    </div>

    <footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
        &copy; {{ date('Y') }} SICEBU SMKN 1 Talaga.
    </footer>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let chart;
        const initData = {
            categories: @json($chartData['categories']),
            incomes: @json($chartData['incomes']),
            expenses: @json($chartData['expenses'])
        };
        renderChart(initData);

        function renderChart(data) {
            const options = {
                series: [{ name: 'Pemasukan', data: data.incomes }, { name: 'Pengeluaran', data: data.expenses }],
                chart: { type: 'area', height: 250, toolbar: { show: false }, zoom: { enabled: false } }, // Height 250px (Kecil)
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: { categories: data.categories, axisBorder: { show: false }, axisTicks: { show: false } },
                yaxis: { labels: { formatter: val => val >= 1000 ? (val/1000).toFixed(0) + "k" : val } },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.6, opacityTo: 0.1, stops: [0, 90, 100] } },
                colors: ['#00E396', '#FF4560'],
                tooltip: { y: { formatter: val => "Rp " + val.toLocaleString('id-ID') } },
                grid: { borderColor: '#f1f1f1', padding: { top: 0, right: 0, bottom: 0, left: 10 } },
                legend: { position: 'top', horizontalAlign: 'right' }
            };

            if (chart) { chart.updateOptions(options); } 
            else { chart = new ApexCharts(document.querySelector("#financialChart"), options); chart.render(); }
        }

        window.updateChart = function(range, btn) {
            document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            fetch("{{ route('api.chart') }}?range=" + range)
                .then(response => response.json())
                .then(data => { renderChart(data); })
                .catch(error => console.error('Error:', error));
        }
    });
    </script>
</body>
</html>