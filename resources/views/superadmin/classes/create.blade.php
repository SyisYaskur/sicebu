@extends('layouts.admin.app')

@section('title', 'Tambah Kelas Baru')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Kelas Baru</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.classes.index') }}">Kelas</a></li>
        <li class="breadcrumb-item active">Tambah Kelas</li>
    </ol>

    <div class="card mb-4 col-lg-8"> {{-- Batasi lebar card --}}
        <div class="card-header">
            <i class="fas fa-plus me-1"></i> Form Tambah Kelas
        </div>
        <div class="card-body">
             @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Terdapat kesalahan pada input Anda:
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('superadmin.classes.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: TKR 1">
                         @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="academic_level" class="form-label">Tingkat <span class="text-danger">*</span></label>
                         <select class="form-select @error('academic_level') is-invalid @enderror" id="academic_level" name="academic_level" required>
                             <option value="" disabled selected>-- Pilih Tingkat --</option>
                             <option value="10" {{ old('academic_level') == '10' ? 'selected' : '' }}>10</option>
                             <option value="11" {{ old('academic_level') == '11' ? 'selected' : '' }}>11</option>
                             <option value="12" {{ old('academic_level') == '12' ? 'selected' : '' }}>12</option>
                             <option value="13" {{ old('academic_level') == '13' ? 'selected' : '' }}>13</option>
                         </select>
                         @error('academic_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="academic_year" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', date('Y').'/'.(date('Y')+1)) }}" required placeholder="YYYY/YYYY">
                         @error('academic_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="teacher_name" class="form-label">Nama Wali Kelas</label>
                        <input type="text" class="form-control @error('teacher_name') is-invalid @enderror" id="teacher_name" name="teacher_name" value="{{ old('teacher_name') }}">
                         @error('teacher_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="nip_number" class="form-label">NIP Wali Kelas</label>
                        <input type="text" class="form-control @error('nip_number') is-invalid @enderror" id="nip_number" name="nip_number" value="{{ old('nip_number') }}">
                         @error('nip_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="nuptk_number" class="form-label">NUPTK Wali Kelas</label>
                        <input type="text" class="form-control @error('nuptk_number') is-invalid @enderror" id="nuptk_number" name="nuptk_number" value="{{ old('nuptk_number') }}">
                         @error('nuptk_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="col-md-6">
                        <label for="expertise_program_id" class="form-label">Program Keahlian</label>
                         <select class="form-select @error('expertise_program_id') is-invalid @enderror" id="expertise_program_id" name="expertise_program_id">
                             <option value="" selected>-- Pilih Program --</option>
                             @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ old('expertise_program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                             @endforeach
                         </select>
                         @error('expertise_program_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                      <div class="col-md-6">
                        <label for="expertise_concentration_id" class="form-label">Konsentrasi Keahlian</label>
                         <select class="form-select @error('expertise_concentration_id') is-invalid @enderror" id="expertise_concentration_id" name="expertise_concentration_id">
                             <option value="" selected>-- Pilih Konsentrasi --</option>
                              @foreach ($concentrations as $concentration)
                                <option value="{{ $concentration->id }}" {{ old('expertise_concentration_id') == $concentration->id ? 'selected' : '' }}>{{ $concentration->name }}</option>
                             @endforeach
                         </select>
                         @error('expertise_concentration_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                     <a href="{{ route('superadmin.classes.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection