@extends('layouts.admin.app')
@section('title', 'Daftar Penyaluran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Penyaluran /</span> Riwayat</h4>
    @include('components.alert')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="mb-0">Riwayat Penyaluran Dana</h5>
            
            <div class="d-flex align-items-center gap-3">
                {{-- Form Pencarian --}}
                <form action="{{ route('superadmin.disbursements.index') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari Tujuan / Tanggal..." value="{{ $search ?? '' }}">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </div>
                </form>

                <a href="{{ route('superadmin.disbursements.create') }}" class="btn btn-primary text-nowrap">
                    <i class="bx bx-plus me-1"></i> Buat Baru
                </a>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Tujuan</th>
                        <th>Total Disalurkan</th>
                        <th>Bukti</th>
                        <th>Pencatat</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disbursements as $index => $d)
                    <tr>
                        <td>{{ $disbursements->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($d->disbursement_date)->format('d M Y') }}</td>
                        <td><strong>{{ $d->purpose }}</strong></td>
                        <td class="text-primary fw-bold">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</td>
                        
                        {{-- Kolom Bukti --}}
                        <td>
                            @if($d->proof_image)
                                <button type="button" class="btn btn-icon btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#proofModal" 
                                        data-image-url="{{ Storage::url($d->proof_image) }}"
                                        data-description="{{ $d->purpose }}"
                                        title="Lihat Bukti">
                                    <i class="bx bx-image"></i>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>{{ $d->creator->name ?? '-' }}</td>
                        <td class="text-center">
                             <a href="{{ route('superadmin.disbursements.show', $d->id) }}" class="btn btn-icon btn-sm btn-info" title="Detail"><i class="bx bx-show"></i></a>
                             <a href="{{ route('superadmin.disbursements.edit', $d->id) }}" class="btn btn-icon btn-sm btn-warning" title="Edit"><i class="bx bx-edit"></i></a>
                             <form action="{{ route('superadmin.disbursements.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus? Dana akan dikembalikan ke kelas.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-sm btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                             </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bx bx-search fs-3 mb-2"></i><br>
                            Tidak ada data penyaluran ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $disbursements->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Panggil Modal Bukti --}}
@include('superadmin.disbursements._proof_modal')

@push('scripts')
<script>
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