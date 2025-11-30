@extends('layouts.admin.app')
@section('title', 'Edit Pemasukan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Pemasukan /</span> Edit Data</h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Edit Pemasukan: {{ $class->full_name }}</h5></div>
                <div class="card-body">
                    @include('components.alert')
                    
                    <form action="{{ route('superadmin.incomes.update', $income->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ $income->date->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Uang (Rp)</label>
                            <input type="text" class="form-control amount-input" id="amount_display" value="{{ number_format($income->amount, 0, ',', '.') }}" required autocomplete="off">
                            <input type="hidden" name="amount" id="amount_hidden" value="{{ (int)$income->amount }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" rows="3">{{ $income->description }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount_hidden');

    displayInput.addEventListener('input', function(e) {
        let val = this.value.replace(/\D/g, "");
        hiddenInput.value = val;
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
</script>
@endpush