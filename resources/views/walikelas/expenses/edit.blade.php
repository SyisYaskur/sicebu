@extends('layouts.teacher.app')
    @section('title', 'Edit Pengeluaran')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengeluaran /</span> Edit Data</h4>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Form Edit Pengeluaran ({{ $class->name }})</h5></div>
                <div class="card-body">
                    @include('components.alert', ['hide_validation_errors' => true])
                    
                    <form action="{{ route('walikelas.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data" id="expenseForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Dicatat Oleh</label>
                            <input type="text" class="form-control" value="{{ $expense->creator->name ?? 'N/A' }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="expense_date">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amount_display">Jumlah Uang (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount_display" value="{{ old('amount', (int)$expense->amount) }}" required autocomplete="off">
                            <input type="hidden" name="amount" id="amount_hidden" value="{{ old('amount', (int)$expense->amount) }}">
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="recipient">Diberikan Kepada (Penerima) <span class="text-danger">*</span></label>
                            <select id="recipient" name="recipient" class="form-select @error('recipient') is-invalid @enderror" required>
                                <option value="" disabled>-- Pilih Penerima --</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient }}" {{ old('recipient', $expense->recipient) == $recipient ? 'selected' : '' }}>
                                        {{ $recipient }}
                                    </option>
                                @endforeach
                                <option value="Siswa" {{ old('recipient', $expense->recipient) == 'Siswa' ? 'selected' : '' }}>Siswa (Diberikan ke siswa)</option>
                            </select>
                            @error('recipient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- REVISI: Dropdown Siswa (Conditional) --}}
                        <div class="mb-3" id="student_dropdown_wrapper" style="display: none;"> {{-- Sembunyikan --}}
                            <label class="form-label" for="student_id">Pilih Siswa <span class="text-danger">*</span></label>
                            <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror">
                                <option value="" disabled selected>-- Pilih Siswa di Kelas Anda --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', $expense->student_id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="description">Keterangan <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $expense->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="proof_image">Upload Bukti/Bon Baru (Opsional)</label>
                            <input type="file" class="form-control @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah bukti. Max: 2MB.</small>
                            @error('proof_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            
                            @if($expense->proof_image)
                            <div class="mt-2">
                                <small>Bukti Saat Ini:</small>
                                <a href="{{ Storage::url($expense->proof_image) }}" target="_blank">Lihat Bukti</a>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('walikelas.expenses.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    @push('scripts')
    {{-- Script untuk format angka (Sama seperti di Pemasukan) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const displayInput = document.getElementById('amount_display');
        const hiddenInput = document.getElementById('amount_hidden');
        function formatNumber(value) {
            let rawValue = value.toString().replace(/[^0-9]/g, ''); 
            hiddenInput.value = rawValue === '' ? '0' : rawValue;
            if (rawValue === '') { return ''; }
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
        if (displayInput.value) {
            displayInput.value = formatNumber(displayInput.value);
        }
    });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const recipientSelect = document.getElementById('recipient');
    const studentWrapper = document.getElementById('student_dropdown_wrapper');

    function toggleStudentDropdown() {
        if (recipientSelect.value === 'Siswa') {
            studentWrapper.style.display = 'block'; // Tampilkan
        } else {
            studentWrapper.style.display = 'none'; // Sembunyikan
        }
    }
    recipientSelect.addEventListener('change', toggleStudentDropdown);
    toggleStudentDropdown(); // Panggil saat load
});
</script>
    @endpush