@extends('layouts.pengelola.app')
@section('title', 'Daftar Penyaluran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Penyaluran /</span> Riwayat</h4>
    @include('components.alert')

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Riwayat Penyaluran Dana</h5>
            <a href="{{ route('pengelola.disbursements.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Buat Penyaluran Baru
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Tujuan</th>
        <th>Total Disalurkan</th>
        <th>Bukti</th>
        <th>Pencatat</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
@forelse($disbursements as $d)
<tr>
    <td>{{ $loop->iteration + ($disbursements->currentPage() - 1) * $disbursements->perPage() }}</td>
    <td>{{ \Carbon\Carbon::parse($d->disbursement_date)->format('d M Y') }}</td>
    <td><strong>{{ $d->purpose }}</strong></td>
    <td class="text-primary fw-bold">Rp {{ number_format($d->total_amount, 0, ',', '.') }}</td>
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
    <td>
        <a href="{{ route('pengelola.disbursements.show', $d->id) }}" class="btn btn-icon btn-sm btn-info"><i class="bx bx-show"></i></a>
        <a href="{{ route('pengelola.disbursements.edit', $d->id) }}" class="btn btn-icon btn-sm btn-warning"><i class="bx bx-edit"></i></a>
        <form action="{{ route('pengelola.disbursements.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus? Dana akan dikembalikan ke kelas.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-icon btn-sm btn-danger"><i class="bx bx-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center">Belum ada data penyaluran.</td></tr>
@endforelse
</tbody>

            </table>
        </div>
        <div class="card-footer">{{ $disbursements->links() }}</div>
    </div>
</div>
@endsection

@include('pengelola.disbursements._proof_modal')

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
            modalNewTabButton.href = imageUrl;
        });
    }
});
</script>
@endpush