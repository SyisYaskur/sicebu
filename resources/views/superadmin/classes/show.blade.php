@extends('layouts.admin.app')
@section('title', 'Detail Kelas: ' . $class->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Master Kelas /</span> Detail Kelas
        </h4>
        <a href="{{ route('superadmin.classes.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>

    @include('components.alert')

    <div class="row">
        {{-- 1. INFORMASI KELAS LENGKAP --}}
        <div class="col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">Informasi Kelas: {{ $class->full_name }}</h5>
                    <a href="{{ route('superadmin.classes.edit',[$class->id, 'redirect' => 'show']) }}" class="btn btn-sm btn-light text-primary fw-bold">
                        <i class="bx bx-edit me-1"></i> Edit Data
                    </a>
                </div>
                <div class="card-body mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Nama Kelas</dt>
                                <dd class="col-sm-8">{{ $class->full_name }}</dd>

                                <dt class="col-sm-4">Tingkat</dt>
                                <dd class="col-sm-8">{{ $class->academic_level }}</dd>

                                <dt class="col-sm-4">Thn. Akademik</dt>
                                <dd class="col-sm-8"><span class="badge bg-label-primary">{{ $class->academic_year }}</span></dd>

                                <dt class="col-sm-4">Program Keahlian</dt>
                                <dd class="col-sm-8">{{ $class->expertiseProgram?->name ?? '-' }}</dd>
                                
                                <dt class="col-sm-4">Konsentrasi</dt>
                                <dd class="col-sm-8">{{ $class->expertiseConcentration?->name ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Wali Kelas</dt>
                                <dd class="col-sm-8 fw-bold text-primary">{{ $class->teacher_name ?? 'Belum Ditentukan' }}</dd>

                                <dt class="col-sm-4">NIP Wali Kelas</dt>
                                <dd class="col-sm-8">{{ $class->nip_number ?? '-' }}</dd>

                                <dt class="col-sm-4">NUPTK Wali Kelas</dt>
                                <dd class="col-sm-8">{{ $class->nuptk_number ?? '-' }}</dd>

                                <dt class="col-sm-4">Jumlah Siswa</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-label-success fs-6">{{ $students->count() }} Siswa</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TABEL SISWA DETAIL --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Siswa Lengkap</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Nama Lengkap</th>
                                <th>Gender</th>
                                <th>Tempat, Tgl Lahir</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $student->student_number ?? '-' }}</td>
                                <td>{{ $student->national_student_number ?? '-' }}</td>
                                <td><strong>{{ $student->full_name }}</strong></td>
                                <td>{{ $student->gender ?? '-' }}</td>
                                <td>{{ $student->birth_place_date ?? '-' }}</td>
                                <td style="white-space: normal; max-width: 250px;">
                                    {{ $student->address ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-user-x fs-1 mb-2"></i><br>
                                    Belum ada siswa yang terdaftar di kelas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection