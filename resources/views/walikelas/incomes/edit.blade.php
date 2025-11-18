@extends('layouts.teacher.app')
@section('title', 'Edit Pemasukan')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pemasukan /</span> Edit Data</h4>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Form Edit Pemasukan ({{ $class->name }})</h5></div>
                <div class="card-body">
                    @include('components.alert', ['hide_validation_errors' => true])
                    
                    <form action="{{ route('walikelas.incomes.update', $income->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- REVISI PERBAIKAN BUG --}}
                        <div class="mb-3">
                            <label class="form-label" for="amount_display">Jumlah Uang Terkumpul (Rp) <span class="text-danger">*</span></label>
                            
                            {{-- 1. Input Tampilan: Casting (int) untuk membuang ,00 --}}
                            <input type="text" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount_display" 
                                   {{-- BUG FIX: tambahkan (int) untuk membuang desimal .00 --}}
                                   value="{{ old('amount', (int)$income->amount ?? 0) }}" 
                                   placeholder="Contoh: 30.000" 
                                   required 
                                   autocomplete="off">
                            
                            {{-- 2. Input Tersembunyi: Casting (int) juga --}}
                            <input type="hidden" 
                                   name="amount" 
                                   id="amount_hidden" 
                                   {{-- BUG FIX: tambahkan (int) untuk membuang desimal .00 --}}
                                   value="{{ old('amount', (int)$income->amount ?? 0) }}">
                            
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- Akhir Revisi --}}

                        <div class="mb-3">
                            <label class="form-label" for="description">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: Uang kas harian">{{ old('description', $income->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dicatat Oleh</label>
                            <input type="text" class="form-control" value="{{ $income->creator->name ?? 'N/A' }}" readonly disabled>
                        </div>
                        <a href="{{ route('walikelas.incomes.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script JS-nya biarkan sama, tidak perlu diubah --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount_hidden');

    function formatNumber(value) {
        let rawValue = value.toString().replace(/[^0-9]/g, ''); 
        hiddenInput.value = rawValue === '' ? '0' : rawValue;
        
        if (rawValue === '') {
            return '';
        }
        return parseInt(rawValue, 10).toLocaleString('id-ID');
    }

    displayInput.addEventListener('input', function (e) {
        let selectionStart = e.target.selectionStart;
        let originalLength = e.target.value.length;
        e.target.value = formatNumber(e.target.value);
        let newLength = e.target.value.length;
        let diff = newLength - originalLength;
        e.target.setSelectionRange(selectionStart + diff, selectionStart + diff);
    });

    // Format nilai awal saat halaman dimuat
    // Karena Blade sudah memberi nilai (int) "1000", script ini akan mengubahnya jadi "1.000"
    if (displayInput.value) {
        displayInput.value = formatNumber(displayInput.value);
    }
});
</script>
@endpush