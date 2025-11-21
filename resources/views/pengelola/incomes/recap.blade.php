@extends('layouts.pengelola.app')
@section('title', 'Rekap Pemasukan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Rekap Pemasukan</h4>

    {{-- Form Filter Kompleks --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pengelola.income-recap.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    {{-- Tanggal --}}
                    <div class="col-md-3">
                        <label class="form-label">Mulai Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    
                    {{-- Kelas --}}
                    <div class="col-md-3">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pagination Size --}}
                    <div class="col-md-3">
                        <label class="form-label">Tampilkan per Halaman</label>
                        <select name="per_page" class="form-select" {{ $classId ? 'disabled' : '' }} title="{{ $classId ? 'Otomatis Semua jika pilih kelas' : '' }}">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 Data</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 Data</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 Data</option>
                        </select>
                        @if($classId) <input type="hidden" name="per_page" value="1000"> @endif
                    </div>

                    {{-- Nominal Range --}}
                    <div class="col-md-3">
                        <label class="form-label">Min. Nominal (Rp)</label>
                        <input type="text" name="min_amount" class="form-control amount-input" value="{{ $minAmount }}" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max. Nominal (Rp)</label>
                        <input type="text" name="max_amount" class="form-control amount-input" value="{{ $maxAmount }}" placeholder="Tidak terbatas">
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bx bx-filter-alt me-1"></i> Tampilkan
                        </button>
                        
                        {{-- Tombol Download PDF (Submit form ke route PDF) --}}
                        <button type="button" onclick="submitPDF()" class="btn btn-danger">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </button>
                    </div>
                </div>

                <hr class="my-3">
                
                {{-- Filter Kolom (Toggle) --}}
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold text-muted small text-uppercase">Tampilkan Kolom:</span>
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox" name="show_no" value="1" id="colNo" checked>
                        <label class="form-check-label" for="colNo">Nomor</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input column-toggle" type="checkbox" name="show_desc" value="1" id="colDesc" checked>
                        <label class="form-check-label" for="colDesc">Keterangan</label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Rekap --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Hasil Rekap Pemasukan</h5>
            <span class="badge bg-label-success fs-6">Total: Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="col-no text-center" style="width: 5%">No</th>
                        <th class="text-center" style="width: 15%">Tanggal</th>
                        <th style="width: 20%">Kelas</th>
                        <th class="col-desc">Keterangan</th>
                        <th class="text-end" style="width: 20%">Jumlah (Rp)</th>
                        {{-- Total Saldo (Running Balance tidak relevan jika difilter acak, jadi kita tampilkan kumulatif baris ini saja atau kosongkan) --}}
                        {{-- Sesuai request: "Total Saldo". Tapi ingat, jika difilter, saldo berjalan jadi tidak akurat. Kita tampilkan Jumlah saja agar aman. --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $index => $income)
                    <tr>
                        <td class="col-no text-center">{{ $incomes->firstItem() + $index }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                        <td>{{ $income->classRoom->full_name }}</td>
                        <td class="col-desc">{{ $income->description ?? '-' }}</td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($income->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">Tidak ada data yang sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $incomes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Script Format Angka (Ribuan)
    document.querySelectorAll('.amount-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/\D/g, "");
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });

    // 2. Script Toggle Kolom (Live Preview di Tabel Web)
    const toggles = document.querySelectorAll('.column-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const targetClass = this.id === 'colNo' ? '.col-no' : '.col-desc';
            const elements = document.querySelectorAll(targetClass);
            elements.forEach(el => {
                el.style.display = this.checked ? '' : 'none';
            });
        });
    });

    // 3. Script Submit ke PDF
    function submitPDF() {
        const form = document.getElementById('filterForm');
        const originalAction = form.action;
        
        // Ubah tujuan form ke route PDF
        form.action = "{{ route('pengelola.income-recap.pdf') }}";
        form.target = "_blank"; // Buka di tab baru
        form.submit();

        // Kembalikan tujuan form (agar tombol Tampilkan tetap bekerja normal)
        form.action = originalAction;
        form.target = "_self";
    }
</script>
@endpush