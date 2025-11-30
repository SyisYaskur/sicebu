@extends('layouts.admin.app')
@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Pengeluaran /</span> Edit Data</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Edit Pengeluaran: {{ $class->full_name }}</h5></div>
                <div class="card-body">
                    @include('components.alert')
                    
                    <form action="{{ route('superadmin.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Uang (Rp)</label>
                            <input type="text" class="form-control amount-input" id="amount_display" value="{{ number_format($expense->amount, 0, ',', '.') }}" required autocomplete="off">
                            <input type="hidden" name="amount" id="amount_hidden" value="{{ (int)$expense->amount }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penerima</label>
                            <select id="recipient" name="recipient" class="form-select" required>
                                <option value="" disabled>-- Pilih Penerima --</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient }}" {{ old('recipient', $expense->recipient) == $recipient ? 'selected' : '' }}>{{ $recipient }}</option>
                                @endforeach
                                <option value="Siswa" {{ old('recipient', $expense->recipient) == 'Siswa' ? 'selected' : '' }}>Siswa (Diberikan ke siswa)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="student_dropdown_wrapper" style="display: {{ $expense->recipient == 'Siswa' ? 'block' : 'none' }};">
                            <label class="form-label">Pilih Siswa</label>
                            <select name="student_id" class="form-select select2">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', $expense->student_id) == $student->id ? 'selected' : '' }}>{{ $student->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" required>{{ $expense->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti (Opsional)</label>
                            <input type="file" name="proof_image" class="form-control">
                            @if($expense->proof_image)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($expense->proof_image) }}" target="_blank">Lihat Bukti Saat Ini</a>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('superadmin.expenses.index', ['class_id' => $class->id]) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<script>
    $(document).ready(function() { $('.select2').select2({ theme: 'bootstrap-5', width: '100%' }); });

    // Format Angka
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount_hidden');
    displayInput.addEventListener('input', function(e) {
        let val = this.value.replace(/\D/g, "");
        hiddenInput.value = val;
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });

    // Toggle Siswa
    const recipientSelect = document.getElementById('recipient');
    const studentWrapper = document.getElementById('student_dropdown_wrapper');
    recipientSelect.addEventListener('change', function() {
        studentWrapper.style.display = this.value === 'Siswa' ? 'block' : 'none';
    });
</script>
@endpush