@extends('layouts.pengelola.app')
@section('title', 'Laporan Keuangan Sekolah')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Laporan Keuangan Sekolah</h4>

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pengelola.reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">-- Tampilkan Semua Kelas (Rekap) --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ $selectedClassId == $cls->id ? 'selected' : '' }}>
                                {{ $cls->full_name }} {{-- GUNAKAN ACCESSOR INI --}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </form>
            
            <div class="mt-3 text-end">
                <a href="{{ route('pengelola.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-danger btn-sm">
                    <i class="bx bxs-file-pdf"></i> Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- MODE 1: DETAIL SATU KELAS (Micro) --}}
    @if($selectedClassId && isset($class))
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white">Total Pemasukan</h6>
                        <h3 class="text-white">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white">Total Pengeluaran</h6>
                        <h3 class="text-white">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body pb-0">
                        <h6 class="card-title text-white">Sisa Saldo</h6>
                        <h3 class="text-white">Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Tabel Pemasukan --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Rincian Pemasukan: {{ $class->name }}</h5></div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Tgl</th><th>Jml</th><th>Ket</th></tr></thead>
                            <tbody>
                                @foreach($incomes as $inc)
                                <tr>
                                    <td>{{ $inc->date->format('d/m/y') }}</td>
                                    <td>{{ number_format($inc->amount, 0, ',', '.') }}</td>
                                    <td>{{ Str::limit($inc->description, 20) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-2">{{ $incomes->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            </div>
             {{-- Tabel Pengeluaran --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Rincian Pengeluaran: {{ $class->name }}</h5></div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Tgl</th><th>Jml</th><th>Ket</th></tr></thead>
                            <tbody>
                                @foreach($expenses as $exp)
                                <tr>
                                    <td>{{ $exp->expense_date->format('d/m/y') }}</td>
                                    <td>{{ number_format($exp->amount, 0, ',', '.') }}</td>
                                    <td>{{ Str::limit($exp->description, 20) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-2">{{ $expenses->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

    {{-- MODE 2: REKAP SEMUA KELAS (Macro) --}}
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rekapitulasi Semua Kelas</h5>
                <span class="badge bg-label-primary">Total Saldo Sekolah: Rp {{ number_format($grandTotalIncome - $grandTotalExpense, 0, ',', '.') }}</span>
            </div>
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
                            <td><strong>{{ $row->name }}</strong></td>
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