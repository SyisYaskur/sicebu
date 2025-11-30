@extends('layouts.admin.app')
@section('title', 'Tambah Pengeluaran')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Pengeluaran /</span> Tambah Data</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Form Pengeluaran: {{ $class->full_name }}</h5></div>
                <div class="card-body">
                    @include('components.alert')
                    
                    <form action="{{ route('superadmin.expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $class->id }}">

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Uang (Rp)</label>
                            <input type="text" class="form-control amount-input" id="amount_display" placeholder="0" required autocomplete="off">
                            <input type="hidden" name="amount" id="amount_hidden">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penerima</label>
                            <select id="recipient" name="recipient" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Penerima --</option>
                                @foreach($recipients as $recipient)
                                    <option value="{{ $recipient }}">{{ $recipient }}</option>
                                @endforeach
                                <option value="Siswa">Siswa (Diberikan ke siswa)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="student_dropdown_wrapper" style="display: none;">
                            <label class="form-label">Pilih Siswa</label>
                            <select name="student_id" class="form-select select2">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" placeholder="Contoh: Beli Spidol" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti (Opsional)</label>
                            <input type="file" name="proof_image" class="form-control">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    });

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