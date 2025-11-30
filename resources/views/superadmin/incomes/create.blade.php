@extends('layouts.admin.app')
@section('title', 'Tambah Pemasukan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Pemasukan /</span> Tambah Data</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Pemasukan: {{ $class->full_name }}</h5>
                </div>
                <div class="card-body">
                    @include('components.alert')
                    
                    <form action="{{ route('superadmin.incomes.store') }}" method="POST">
                        @csrf
                        {{-- Input Hidden Class ID --}}
                        <input type="hidden" name="class_id" value="{{ $class->id }}">

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Uang (Rp)</label>
                            <input type="text" class="form-control amount-input" id="amount_display" placeholder="0" required autocomplete="off">
                            <input type="hidden" name="amount" id="amount_hidden">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" placeholder="Contoh: Uang Kas Mingguan" rows="3"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('superadmin.incomes.index', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script Format Angka (Ribuan)
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount_hidden');

    displayInput.addEventListener('input', function(e) {
        let val = this.value.replace(/\D/g, "");
        hiddenInput.value = val;
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
</script>
@endpush