@extends('layouts.pengelola.app')
@section('title', 'Detail Penyaluran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Penyaluran /</span> Detail</h4>
        
        {{-- Tombol Cetak PDF --}}
        <a href="{{ route('pengelola.disbursements.pdf', $disbursement->id) }}" target="_blank" class="btn btn-danger">
            <i class="bx bxs-file-pdf me-1"></i> Cetak Laporan (PDF)
        </a>
    </div>

    {{-- Info Header --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 class="mb-2">Tujuan: {{ $disbursement->purpose }}</h5>
                    <p class="text-muted mb-1">
                        <i class="bx bx-calendar me-1"></i> Tanggal: {{ \Carbon\Carbon::parse($disbursement->disbursement_date)->format('d F Y') }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bx bx-user me-1"></i> Dicatat Oleh: {{ $disbursement->creator->name ?? '-' }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-muted d-block">Total Dana Disalurkan</small>
                    <h2 class="text-primary mb-0">Rp {{ number_format($disbursement->total_amount, 0, ',', '.') }}</h2>
                </div>
            </div>
            @if($disbursement->notes)
                <hr class="my-3">
                <p class="mb-0"><strong class="text-dark">Catatan:</strong> {{ $disbursement->notes }}</p>
            @endif
            
            @if($disbursement->proof_image)
                <hr class="my-3">
                <strong>Bukti Penyaluran:</strong><br>
                <a href="{{ Storage::url($disbursement->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="bx bx-image me-1"></i> Lihat Foto Bukti
                </a>
            @endif
        </div>
    </div>

    {{-- Tabel Rincian --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Sumber Dana (Rincian Per Kelas)</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 15%;">Waktu Transaksi</th>
                        <th style="width: 20%;">Kelas</th>
                        <th class="text-end" style="width: 20%;">Saldo Awal (Rp)</th>
                        <th class="text-end" style="width: 20%;">Diambil (Rp)</th>
                        <th class="text-end" style="width: 20%;">Saldo Akhir (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disbursement->allocations as $index => $allocation)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        
                        {{-- Kolom Waktu Lengkap (Tanggal + Jam) --}}
                        <td>
                            <span class="d-block fw-semibold">{{ $allocation->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ $allocation->created_at->format('H:i:s') }} WIB</small>
                        </td>

                        <td><strong>{{ $allocation->classRoom->full_name ?? '-' }}</strong></td>
                        
                        {{-- Saldo Sebelum --}}
                        <td class="text-end text-secondary">
                            @if($allocation->balance_before !== null)
                                {{ number_format($allocation->balance_before, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Jumlah Diambil --}}
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($allocation->amount_transferred, 0, ',', '.') }}
                        </td>

                        {{-- Saldo Sesudah --}}
                        <td class="text-end text-primary fw-bold">
                            @if($allocation->balance_after !== null)
                                {{ number_format($allocation->balance_after, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">TOTAL</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($disbursement->total_amount, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('pengelola.disbursements.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection