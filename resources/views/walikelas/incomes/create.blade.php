@extends('layouts.teacher.app')
@section('title', 'Tambah Pemasukan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pemasukan /</span> Tambah Data</h4>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Form Tambah Pemasukan ({{ $class->name }})</h5></div>
                <div class="card-body">
                    @include('components.alert', ['hide_validation_errors' => true])
                    
                    <form action="{{ route('walikelas.incomes.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- REVISI (Bagian Ini Berubah) --}}
                        <div class="mb-3">
                            <label class="form-label" for="amount_display">Jumlah Uang Terkumpul (Rp) <span class="text-danger">*</span></label>
                            
                            {{-- 1. Input yang dilihat user (untuk tampilan) --}}
                            <input type="text" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount_display" 
                                   value="{{ old('amount') }}" 
                                   placeholder="Contoh: 30.000" 
                                   required 
                                   autocomplete="off">
                            
                            {{-- 2. Input tersembunyi (untuk dikirim ke database) --}}
                            <input type="hidden" 
                                   name="amount" 
                                   id="amount_hidden" 
                                   value="{{ old('amount') ?? 0 }}">
                            
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- Akhir Revisi --}}

                        <div class="mb-3">
                            <label class="form-label" for="description">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: Uang kas harian">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Wali Kelas (Pencatat)</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly disabled>
                        </div>
                        <a href="{{ route('walikelas.incomes.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- REVISI: Tambahkan script untuk format angka --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount_hidden');

    // Fungsi untuk memformat angka dengan titik (Separator Ribuan)
    function formatNumber(value) {
        // 1. Hapus semua karakter non-digit (misal: "Rp", ".", spasi)
        let rawValue = value.toString().replace(/[^0-9]/g, '');
        
        // 2. Set nilai hidden input dengan angka mentah (raw)
        // Jika kosong, set ke '0' agar validasi 'numeric' lolos
        hiddenInput.value = rawValue === '' ? '0' : rawValue;
        
        // 3. Format angka untuk tampilan (display)
        if (rawValue === '') {
            return ''; // Biarkan kosong jika user menghapus
        }
        // Konversi ke angka, lalu format ke string lokal (id-ID)
        return parseInt(rawValue, 10).toLocaleString('id-ID');
    }

    // Panggil fungsi saat user mengetik (event 'input')
    displayInput.addEventListener('input', function (e) {
        // Ambil posisi kursor sebelum diformat
        let selectionStart = e.target.selectionStart;
        let originalLength = e.target.value.length;

        // Format angka
        e.target.value = formatNumber(e.target.value);
        
        // Logika untuk mempertahankan posisi kursor
        let newLength = e.target.value.length;
        let diff = newLength - originalLength;
        e.target.setSelectionRange(selectionStart + diff, selectionStart + diff);
    });

    // Format nilai awal jika ada (misalnya saat terjadi error validasi)
    if (displayInput.value) {
        displayInput.value = formatNumber(displayInput.value);
    }
});
</script>
@endpush