@extends('layouts.admin.app')

@section('title', 'Tambah Kelas Baru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Kelas /</span> Tambah Kelas</h4>

    <div class="card mb-4 col-lg-8">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Form Tambah Kelas</h5>
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
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: RPL 3">
                        <small class="text-muted">Hanya masukkan nama pembeda (misal: RPL 3, PPLG 3, BR 4). Tingkat akan digabung otomatis.</small> 
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                     <div class="col-md-6">
                        <label for="academic_level" class="form-label">Tingkat <span class="text-danger">*</span></label>
                         <select class="form-select @error('academic_level') is-invalid @enderror" id="academic_level" name="academic_level" required>
                             <option value="" disabled selected>-- Pilih Tingkat --</option>
                             <option value="10" {{ old('academic_level') == '10' ? 'selected' : '' }}>10</option>
                             <option value="11" {{ old('academic_level') == '11' ? 'selected' : '' }}>11</option>
                             <option value="12" {{ old('academic_level') == '12' ? 'selected' : '' }}>12</option>
                         </select>
                         @error('academic_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                     <div class="col-md-6">
                        <label for="academic_year" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', date('Y').'/'.(date('Y')+1)) }}" required placeholder="YYYY/YYYY">
                         @error('academic_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- REVISI: Dropdown Wali Kelas --}}
                    <div class="col-md-12">
                        <label for="user_id" class="form-label">Wali Kelas (Guru)</label>
                        <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">-- Pilih Guru Wali Kelas --</option>
                            @foreach($teachers as $teacher)
                                {{-- PERBAIKAN: Hapus $class->user_id di sini --}}
                                <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                         <div class="row">
                             <div class="col-md-6">
                                <label for="nip_number" class="form-label">NIP (Otomatis/Manual)</label>
                                <input type="text" class="form-control" id="nip_number" name="nip_number" value="{{ old('nip_number') }}">
                             </div>
                             <div class="col-md-6">
                                <label for="nuptk_number" class="form-label">NUPTK (Otomatis/Manual)</label>
                                <input type="text" class="form-control" id="nuptk_number" name="nuptk_number" value="{{ old('nuptk_number') }}">
                             </div>
                         </div>
                    </div>

                     <div class="col-md-6">
                        <label for="expertise_concentration_id" class="form-label">Konsentrasi Keahlian</label>
                         <select class="form-select select2" id="expertise_concentration_id" name="expertise_concentration_id" required>
                             <option value="" selected>-- Pilih Konsentrasi --</option>
                              @foreach ($concentrations as $concentration)
                                {{-- Simpan ID Program di data attribute --}}
                                <option value="{{ $concentration->id }}" data-program-id="{{-- Disini harusnya ada relasi ke program, jika tidak ada di model, kita mapping manual atau biarkan user pilih --}}"> 
                                    {{ $concentration->name }}
                                </option>
                             @endforeach
                         </select>
                    </div>

                    <div class="col-md-6">
                        <label for="expertise_program_id" class="form-label">Program Keahlian</label>
                        {{-- Jika relasi database belum rapi, biarkan manual dulu tapi kasih note --}}
                         <select class="form-select select2" id="expertise_program_id" name="expertise_program_id" required>
                             <option value="" selected>-- Pilih Program --</option>
                             @foreach ($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                             @endforeach
                         </select>
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