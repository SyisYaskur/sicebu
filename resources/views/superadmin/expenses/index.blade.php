@extends('layouts.admin.app')
@section('title', 'Master Pengeluaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Master Pengeluaran</h4>
    
    @include('components.alert')

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('superadmin.expenses.index') }}" method="GET">
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

    {{-- Tombol Tambah --}}
    @if($selectedClass)
        <div class="mb-3">
            <a href="{{ route('superadmin.expenses.create', ['class_id' => $selectedClass->id]) }}" class="btn btn-danger">
                <i class="bx bx-minus-circle me-1"></i> Input Pengeluaran {{ $selectedClass->full_name }}
            </a>
        </div>
    @endif

    {{-- Tabel Data --}}
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Keterangan</th>
                        <th>Penerima</th>
                        <th>Bukti</th>
                        <th class="text-end">Jumlah</th>
                        <th>Dicatat Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $expense)
                    <tr>
                        <td class="text-center">{{ $expenses->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $expense->classRoom->full_name }}</strong></td>
                        <td>{{ $expense->description }}</td>
                        
                        {{-- Penerima --}}
                        <td>
                            @if($expense->recipient == 'Siswa' && $expense->student)
                                {{ $expense->student->full_name }} (Siswa)
                            @else
                                {{ $expense->recipient ?? '-' }}
                            @endif
                        </td>

                        {{-- Bukti (Modal) --}}
                        <td class="text-center">
                            @if($expense->proof_image)
                                <button type="button" class="btn btn-icon btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#proofModal" 
                                        data-image-url="{{ Storage::url($expense->proof_image) }}"
                                        data-description="{{ $expense->description }}">
                                    <i class="bx bx-image"></i>
                                </button>
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-end fw-bold text-danger">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td><small class="text-muted">{{ $expense->creator->name ?? '-' }}</small></td>
                        
                        <td class="text-center">
                            <a href="{{ route('superadmin.expenses.edit', $expense->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('superadmin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pengeluaran ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4">Tidak ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

{{-- Modal Bukti --}}
@include('walikelas.expenses._proof_modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    });

    // Script Modal (Copy dari Wali Kelas)
    document.addEventListener('DOMContentLoaded', function () {
        const proofModal = document.getElementById('proofModal');
        if (proofModal) {
            const modalImage = document.getElementById('modalProofImage');
            const modalDescription = document.getElementById('modalDescription');
            const modalNewTabButton = document.getElementById('modalOpenNewTab');

            proofModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imageUrl = button.getAttribute('data-image-url');
                const description = button.getAttribute('data-description');

                modalImage.src = imageUrl;
                modalDescription.textContent = description;
                if(modalNewTabButton) modalNewTabButton.href = imageUrl;
            });
        }
    });
</script>
@endpush