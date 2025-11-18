@extends('layouts.admin.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Siswa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.students.index') }}">Siswa</a></li>
        <li class="breadcrumb-item active">Edit Siswa</li>
    </ol>

    <div class="card mb-4 col-lg-8"> {{-- Batasi lebar card --}}
        <div class="card-header">
            <i class="fas fa-edit me-1"></i> Form Edit Siswa: {{ $student->full_name }}
        </div>
        <div class="card-body">
            @include('components.alert', ['hide_validation_errors' => true]) {{-- Sembunyikan validasi umum, tampilkan per field --}}

            <form action="{{ route('superadmin.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Kolom Data Siswa (Sesuaikan dengan kebutuhan) --}}
                <h5>Data Pribadi</h5>
                <hr>
                <div class="row g-3 mb-3">
                     <div class="col-md-12">
                        <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $student->full_name) }}" required>
                         @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="student_number" class="form-label">NIS</label>
                        <input type="text" class="form-control @error('student_number') is-invalid @enderror" id="student_number" name="student_number" value="{{ old('student_number', $student->student_number) }}">
                         @error('student_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="national_student_number" class="form-label">NISN</label>
                        <input type="text" class="form-control @error('national_student_number') is-invalid @enderror" id="national_student_number" name="national_student_number" value="{{ old('national_student_number', $student->national_student_number) }}">
                         @error('national_student_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                      <div class="col-md-6">
                        <label for="national_identification_number" class="form-label">NIK</label>
                        <input type="text" class="form-control @error('national_identification_number') is-invalid @enderror" id="national_identification_number" name="national_identification_number" value="{{ old('national_identification_number', $student->national_identification_number) }}">
                         @error('national_identification_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-Laki" {{ old('gender', $student->gender) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                         @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="religion" class="form-label">Agama</label>
                        <input type="text" class="form-control @error('religion') is-invalid @enderror" id="religion" name="religion" value="{{ old('religion', $student->religion) }}">
                         @error('religion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="birth_place_date" class="form-label">Tempat, Tanggal Lahir</label>
                        <input type="text" class="form-control @error('birth_place_date') is-invalid @enderror" id="birth_place_date" name="birth_place_date" value="{{ old('birth_place_date', $student->birth_place_date) }}">
                         @error('birth_place_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-12">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                         @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    {{-- Tambahkan input field lain yang ingin diedit --}}
                </div>

                {{-- Kolom Pindah Kelas --}}
                <h5 class="mt-4">Penempatan Kelas</h5>
                <hr>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="academic_year" class="form-label">Untuk Tahun Ajaran <span class="text-danger">*</span></label>
                        {{-- Idealnya ini dropdown atau diambil dari setting aplikasi --}}
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', $currentAcademicYear) }}" readonly required>
                         <small class="form-text text-muted">Perubahan kelas hanya berlaku untuk tahun ajaran ini.</small>
                         @error('academic_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="class_id" class="form-label">Pindahkan ke Kelas</label>
                        <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id">
                            <option value="">-- Hapus dari Kelas (Tahun Ini) --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $currentAssignment?->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{$class->academic_level}})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih kelas baru atau kosongkan untuk mengeluarkan siswa dari kelas di tahun ajaran ini.</small>
                         @error('class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>


                <div class="d-flex justify-content-end mt-4">
                     <a href="{{ route('superadmin.students.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection