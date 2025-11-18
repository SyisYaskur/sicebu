@extends('layouts.teacher.app')
@section('title', 'Pengeluaran Kelas ' . $class->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kelas Saya /</span> Pengeluaran</h4>
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

    {{-- Daftar Transaksi --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengeluaran</h5>
            <a href="{{ route('walikelas.expenses.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Tambah Pengeluaran</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah (Rp)</th>
                        <th>Keterangan</th>
                        <th>Penerima</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($expenses as $index => $expense)
                    <tr>
                        <td>{{ $expenses->firstItem() + $index }}</td>
                        <td><strong>{{ $expense->expense_date->format('d M Y') }}</strong></td>
                        <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td>{{ $expense->description }}</td>
                        
                        {{-- REVISI: Tampilkan nama siswa jika ada --}}
                        <td>
                            @if($expense->recipient == 'Siswa' && $expense->student)
                                {{ $expense->student->full_name ?? 'Siswa' }}
                            @else
                                {{ $expense->recipient ?? '-' }}
                            @endif
                        </td>

                        <td>
                            @if($expense->proof_image)
                                <button type="button" class="btn btn-icon btn-sm btn-outline-info proof-button" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#proofModal" 
                                        data-image-url="{{ Storage::url($expense->proof_image) }}"
                                        data-description="{{ $expense->description }}"
                                        title="Lihat Bukti">
                                    <i class="bx bx-image"></i>
                                </button>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('walikelas.expenses.edit', $expense->id) }}" class="btn btn-icon btn-sm btn-warning" title="Edit"><i class="bx bx-edit-alt"></i></a>
                            <button type="button" class="btn btn-icon btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $expense->id }}" title="Hapus"><i class="bx bx-trash"></i></button>
                            @include('walikelas.expenses._delete_modal', ['expense' => $expense])
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">Belum ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $expenses->links() }}</div>
    </div>
</div>

{{-- REVISI: Panggil file modal yang kita buat di Langkah 1 --}}
@include('walikelas.expenses._proof_modal')

@endsection

@push('scripts')
{{-- REVISI: Tambahkan script untuk Modal dan Zoom --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const proofModal = document.getElementById('proofModal');
    
    // Pastikan modal ada di halaman ini
    if (proofModal) {
        const modalImage = document.getElementById('modalProofImage');
        const modalDescription = document.getElementById('modalDescription');
        const modalNewTabButton = document.getElementById('modalOpenNewTab'); // Ambil tombol "Buka Tab Baru"

        // 1. Script untuk passing data ke Modal saat dibuka
        proofModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;
            
            // Ambil data dari atribut data-*
            const imageUrl = button.getAttribute('data-image-url');
            const description = button.getAttribute('data-description');

            // Set src gambar
            modalImage.src = imageUrl;
            
            // Set deskripsi di footer
            modalDescription.textContent = description;
            
            // Set link href untuk tombol "Buka di Tab Baru"
            modalNewTabButton.href = imageUrl;
        });

        // 2. (Selesai) Tidak ada lagi script zoom/pan
    }
});
</script>
@endpush