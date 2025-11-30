@extends('layouts.teacher.app')
@section('title', 'Pemasukan Kelas ' . $class->name)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kelas Saya /</span> Pemasukan</h4>
    @include('components.alert')

    {{-- Kartu Saldo --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Pemasukan</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2 text-success">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3></div>
                        </div>
                        <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Pengeluaran</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2 text-danger">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3></div>
                        </div>
                        <span class="badge bg-label-danger rounded p-2"><i class="bx bx-shopping-bag bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Sisa Saldo</span>
                            <div class="d-flex align-items-end mt-2"><h3 class="mb-0 me-2 text-primary">= Rp {{ number_format($balance, 0, ',', '.') }}</h3></div>
                        </div>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-wallet bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pemasukan</h5>
            <a href="{{ route('walikelas.incomes.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Tambah Pemasukan</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($incomes as $index => $income)
                    <tr>
                        <td>{{ $incomes->firstItem() + $index }}</td>
                        <td><strong>{{ $income->date->format('d M Y') }}</strong></td>
                        <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                        <td>{{ $income->description ?? '-' }}</td>
                        <td>{{ $income->creator->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('walikelas.incomes.edit', $income->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bx bx-edit-alt"></i></a>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $income->id }}" title="Hapus"><i class="bx bx-trash"></i></button>
                            @include('walikelas.incomes._delete_modal', ['income' => $income])
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Belum ada data pemasukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $incomes->links() }}</div>
    </div>
</div>
@endsection