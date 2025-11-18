@extends('layouts.teacher.app')

@section('title', 'Kelas Saya: ' . $class->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Kelas Saya: {{ $class->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelas Saya</li>
    </ol>

    @include('components.alert')

    {{-- Informasi Kelas Singkat --}}
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-info-circle me-1"></i> Informasi Kelas ({{ $class->academic_year }})</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Nama Kelas</dt> <dd class="col-sm-9">{{ $class->name }}</dd>
                <dt class="col-sm-3">Tingkat</dt> <dd class="col-sm-9">{{ $class->academic_level }}</dd>
                <dt class="col-sm-3">Wali Kelas</dt> <dd class="col-sm-9">{{ $class->teacher_name }}</dd>
                <dt class="col-sm-3">Jumlah Siswa</dt> <dd class="col-sm-9">{{ $assignedStudents->count() }} Siswa</dd>
            </dl>
        </div>
    </div>

    {{-- Daftar Siswa di Kelas Ini --}}
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-users me-1"></i> Daftar Siswa di Kelas {{ $class->name }}</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS / NISN</th> {{-- UBAH: Judul Kolom --}}
                            <th>Nama Lengkap</th>
                            <th>Gender</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignedStudents as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            {{-- UBAH: Tampilkan kedua nomor --}}
                            <td>{{ $student->student_number ?? '-' }} / {{ $student->national_student_number ?? '-' }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->gender ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#removeModal-{{ $student->id }}" title="Keluarkan dari Kelas">
                                    <i class="fas fa-user-minus"></i> Keluarkan
                                </button>
                                <!-- Modal Konfirmasi Hapus -->
                                @include('walikelas.my_class.remove_student_modal', ['student' => $student, 'class' => $class])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada siswa di kelas ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Tambah Siswa ke Kelas --}}
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-user-plus me-1"></i> Tambah Siswa ke Kelas {{ $class->name }}</div>
        <div class="card-body">
            @if ($unassignedStudents->count() > 0)
                <form action="{{ route('walikelas.my-class.add-student') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="student_id" class="form-label">Pilih Siswa (yang belum punya kelas)</label>
                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                <option value="" disabled selected>-- Cari dan Pilih Siswa --</option>
                                @foreach ($unassignedStudents as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{-- UBAH: Tampilkan kedua nomor --}}
                                        {{ $student->student_number ?? 'NIS?' }} / {{ $student->national_student_number ?? 'NISN?' }} - {{ $student->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Tambahkan ke Kelas
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <p class="text-muted">Semua siswa sudah memiliki kelas untuk tahun ajaran {{ $class->academic_year }}.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Jika menggunakan Select2 untuk dropdown siswa yang banyak --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#student_id').select2({
            placeholder: "-- Cari dan Pilih Siswa --",
            allowClear: true
        });
    });
</script>
@endpush