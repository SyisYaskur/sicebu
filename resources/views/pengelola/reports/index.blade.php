@extends('layouts.pengelola.app')
@section('title', 'Laporan Keuangan Sekolah')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Laporan Keuangan Sekolah</h4>

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pengelola.reports.index') }}" 
      method="GET" 
      class="row g-3 align-items-end justify-content-between">

    <div class="col-md-3">
        <label class="form-label fw-semibold">Tanggal Mulai</label>
        <input type="date" name="start_date" class="form-control shadow-sm" 
               value="{{ $startDate }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Tanggal Akhir</label>
        <input type="date" name="end_date" class="form-control shadow-sm" 
               value="{{ $endDate }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Filter Kelas</label>
        <select name="class_id" class="form-select shadow-sm">
            <option value="">-- Semua Kelas --</option>
            @foreach($classes as $cls)
                <option value="{{ $cls->id }}" 
                        {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                    {{ $cls->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary shadow-sm">
            Tampilkan
        </button>
    </div>
</form>

<div class="mt-3 text-end">
    <a href="{{ route('pengelola.reports.pdf', request()->query()) }}" 
       target="_blank" 
       class="btn btn-danger btn-sm shadow-sm">
        <i class="bx bxs-file-pdf"></i> Download PDF
    </a>
</div>

        </div>
    </div>

    {{-- --- MODE 1: DETAIL SATU KELAS (Mikro - Ledger View) --- --}}
   @if($selectedClassId && isset($class))
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-muted mb-1">Saldo Awal (Sebelum Periode)</h6>
                        <h4 class="text-secondary">Rp {{ number_format($stats['openingBalance'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white mb-1">Total Pemasukan</h6>
                        <h3 class="text-white">Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white mb-1">Total Pengeluaran</h6>
                        <h3 class="text-white">Rp {{ number_format($stats['totalExpense'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white mb-1">Saldo Akhir</h6>
                        <h3 class="text-white">Rp {{ number_format($stats['finalBalance'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Laporan Harian Kelas {{ $class->full_name }}</h5></div>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="width: 20%;" class="text-center">Tanggal</th>
                            {{-- Kolom Keterangan DIHAPUS sesuai permintaan --}}
                            <th style="width: 25%;" class="text-end">Pemasukan (Rp)</th>
                            <th style="width: 25%;" class="text-end">Pengeluaran (Rp)</th>
                            <th style="width: 25%;" class="text-end">Saldo Akhir Hari (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paginatedData as $index => $row)
                        <tr>
                            <td class="text-center">{{ $paginatedData->firstItem() + $index }}</td>
                            <td class="text-center fw-bold">{{ $row->date->format('d F Y') }}</td>
                            
                            {{-- Pemasukan --}}
                            <td class="text-end text-success">
                                {{ $row->income > 0 ? number_format($row->income, 0, ',', '.') : '-' }}
                            </td>
                            
                            {{-- Pengeluaran --}}
                            <td class="text-end text-danger">
                                {{ $row->expense > 0 ? number_format($row->expense, 0, ',', '.') : '-' }}
                            </td>
                            
                            {{-- Saldo Berjalan --}}
                            <td class="text-end fw-bolder text-primary">
                                Rp {{ number_format($row->balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $paginatedData->appends(request()->query())->links() }}
            </div>
        </div>

    {{-- MODE 2: REKAP SEMUA KELAS (Macro) --}}
    @else
        <div class="card">
            {{-- ... (header card biarkan sama) ... --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th class="text-end">Pemasukan</th>
                            <th class="text-end">Pengeluaran</th>
                            <th class="text-end">Saldo Akhir</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recapData as $row)
                            @php
                                $inc = $row->incomes_sum_amount ?? 0;
                                $exp = $row->expenses_sum_amount ?? 0;
                                $bal = $inc - $exp;
                            @endphp
                        <tr>
                            {{-- PERBAIKAN DI SINI: Gunakan full_name --}}
                            <td><strong>{{ $row->full_name }}</strong></td> 
                            
                            <td>{{ $row->teacher_name ?? '-' }}</td>
                            <td class="text-end text-success">{{ number_format($inc, 0, ',', '.') }}</td>
                            <td class="text-end text-danger">{{ number_format($exp, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-primary">{{ number_format($bal, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('pengelola.reports.index', array_merge(request()->query(), ['class_id' => $row->id])) }}" class="btn btn-sm btn-outline-info">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">Tidak ada data kelas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $recapData->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection