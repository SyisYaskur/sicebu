@extends('layouts.admin.app')

@section('title', 'Detail Kelas: ' . $class->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Kelas: {{ $class->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('superadmin.classes.index') }}">Kelas</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>

    {{-- Card Informasi Kelas --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i> Informasi Kelas
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Nama Kelas</dt>
                        <dd class="col-sm-8">{{ $class->name }}</dd>

                        <dt class="col-sm-4">Tingkat</dt>
                        <dd class="col-sm-8">{{ $class->academic_level ?? '-' }}</dd>

                        <dt class="col-sm-4">Thn. Akademik</dt>
                        <dd class="col-sm-8">{{ $class->academic_year ?? '-' }}</dd>

                        <dt class="col-sm-4">Program Keahlian</dt>
                        <dd class="col-sm-8">{{ $class->expertiseProgram?->name ?? '-' }}</dd>

                        <dt class="col-sm-4">Konsentrasi</dt>
                        <dd class="col-sm-8">{{ $class->expertiseConcentration?->name ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                     <dl class="row">
                        <dt class="col-sm-4">Wali Kelas</dt>
                        <dd class="col-sm-8">{{ $class->teacher_name ?? '-' }}</dd>

                        <dt class="col-sm-4">NIP</dt>
                        <dd class="col-sm-8">{{ $class->nip_number ?? '-' }}</dd>

                        <dt class="col-sm-4">NUPTK</dt>
                        <dd class="col-sm-8">{{ $class->nuptk_number ?? '-' }}</dd>

                        <dt class="col-sm-4">Jumlah Siswa</dt>
                        <dd class="col-sm-8"><strong>{{ $students->count() }} Siswa</strong></dd>
                     </dl>
                </div>
            </div>
        </div>
    </div>

     {{-- Card Daftar Siswa --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i> Daftar Siswa di Kelas Ini ({{ $class->academic_year }})
        </div>
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped"> {{-- Tambah striped --}}
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 20%;">NISN / NIS</th>
                            <th>Nama Lengkap</th>
                            <th style="width: 15%;">Jenis Kelamin</th>
                            {{-- Tambah kolom lain jika perlu --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->national_student_number ?? ($student->student_number ?? '-') }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->gender ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada siswa yang terdaftar di kelas ini untuk tahun ajaran {{ $class->academic_year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

     {{-- Tombol Aksi Bawah --}}
     <div class="mt-3 d-flex justify-content-between">
         <a href="{{ route('superadmin.classes.index') }}" class="btn btn-secondary">
             <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas
         </a>
         <a href="{{ route('superadmin.classes.edit', $class->id) }}" class="btn btn-warning">
             <i class="fas fa-edit"></i> Edit Kelas & Siswa
         </a>
     </div>

</div>
@endsection