@extends('layouts.admin.app')
@section('title', 'Master Pemasukan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Master Pemasukan</h4>
    
    @include('components.alert')

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('superadmin.incomes.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select select2"> 
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tombol Tambah (Hanya muncul jika kelas dipilih) --}}
    @if($selectedClass)
        <div class="mb-3">
            <a href="{{ route('superadmin.incomes.create', ['class_id' => $selectedClass->id]) }}" class="btn btn-success">
                <i class="bx bx-plus me-1"></i> Input Pemasukan {{ $selectedClass->full_name }}
            </a>
        </div>
    @endif

    {{-- Tabel Data --}}
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Kelas</th>
                        <th>Keterangan</th>
                        <th width="15%" class="text-end">Jumlah</th>
                        <th width="15%">Dicatat Oleh</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $index => $income)
                    <tr>
                        <td class="text-center">{{ $incomes->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $income->classRoom->full_name }}</strong></td>
                        <td>{{ $income->description ?? '-' }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                        <td><small class="text-muted">{{ $income->creator->name ?? '-' }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('superadmin.incomes.edit', $income->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('superadmin.incomes.destroy', $income->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pemasukan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4">Tidak ada data pemasukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $incomes->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endpush