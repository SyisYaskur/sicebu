@extends('layouts.admin.app')
@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Siswa /</span> Tambah Siswa</h4>
    @include('components.alert')

    <form action="{{ route('superadmin.students.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Biodata Siswa</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required placeholder="Nama Siswa">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIS</label>
                                <input type="text" name="student_number" class="form-control" value="{{ old('student_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NISN</label>
                                <input type="text" name="national_student_number" class="form-control" value="{{ old('national_student_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIK</label>
                                <input type="text" name="national_identification_number" class="form-control" value="{{ old('national_identification_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Laki-Laki">Laki-Laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat, Tgl Lahir</label>
                                <input type="text" name="birth_place_date" class="form-control" value="{{ old('birth_place_date') }}" placeholder="Bandung, 20 Mei 2008">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Agama</label>
                                <select name="religion" class="form-select">
                                    <option value="Islam" selected>Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-label-primary">
                        <h5 class="mb-0 text-primary">Penempatan Kelas</h5>
                    </div>
                    <div class="card-body mt-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Kelas ({{ $currentYear }})</label>
                            <select name="class_id" class="form-select select2">
                                <option value="">-- Tanpa Kelas --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Simpan Siswa</button>
                        <a href="{{ route('superadmin.students.index') }}" class="btn btn-outline-secondary w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Script Select2 yang sama --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endpush