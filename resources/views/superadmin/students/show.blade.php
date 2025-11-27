@extends('layouts.admin.app')
@section('title', 'Detail Siswa: ' . $student->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Master Siswa /</span> Detail Siswa
        </h4>
        <div>
            <a href="{{ route('superadmin.students.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <a href="{{ route('superadmin.students.edit', $student->id) }}" class="btn btn-warning">
                <i class="bx bx-edit me-1"></i> Edit Data
            </a>
        </div>
    </div>

    @include('components.alert')

    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-body text-center">
                    {{-- Avatar Placeholder (Bisa diganti jika ada fitur upload foto siswa) --}}
                    <div class="mx-auto mb-3">
                        <div class="avatar avatar-xl" style="width: 100px; height: 100px;">
                            <span class="avatar-initial rounded-circle bg-label-primary fs-1">
                                {{ substr($student->full_name, 0, 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <h5 class="mb-1 card-title">{{ $student->full_name }}</h5>
                    <span class="badge bg-label-info mb-3">Siswa Aktif</span>

                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary">NIS: {{ $student->student_number ?? '-' }}</span>
                        <span class="badge bg-secondary">NISN: {{ $student->national_student_number ?? '-' }}</span>
                    </div>

                    <div class="mt-4 pt-2 border-top text-start">
                        <p class="mb-1"><i class="bx bx-id-card me-2"></i> <strong>NIK:</strong> {{ $student->national_identification_number ?? '-' }}</p>
                        <p class="mb-1"><i class="bx bx-user me-2"></i> <strong>Gender:</strong> {{ $student->gender }}</p>
                        <p class="mb-1"><i class="bx bx-church me-2"></i> <strong>Agama:</strong> {{ $student->religion ?? '-' }}</p>
                        <p class="mb-1"><i class="bx bx-calendar me-2"></i> <strong>TTL:</strong> {{ $student->birth_place_date ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Riwayat Kelas</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($classHistory as $history)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $history->classRoom->full_name ?? 'Kelas Dihapus' }}</strong>
                                    <br><small class="text-muted">{{ $history->classRoom->teacher_name ?? '-' }}</small>
                                </div>
                                <span class="badge bg-label-success">{{ $history->academic_year }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">Belum ada riwayat kelas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-biodata">
                                Biodata
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-parents">
                                Orang Tua / Wali
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-address">
                                Alamat
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content p-0">
                        {{-- Tab 1: Biodata Fisik & Lainnya --}}
                        <div class="tab-pane fade show active" id="navs-pills-biodata" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-bold">Anak Ke-</label>
                                    <p>{{ $student->birth_order ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Jumlah Saudara</label>
                                    <p>{{ $student->siblings ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Tinggi Badan</label>
                                    <p>{{ $student->height_cm ? $student->height_cm . ' cm' : '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Berat Badan</label>
                                    <p>{{ $student->weight_kg ? $student->weight_kg . ' kg' : '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Golongan Darah</label>
                                    <p>{{ $student->blood_type ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Hobi</label>
                                    <p>{{ $student->hobby ?? '-' }}</p>
                                </div>
                                <div class="col-md-12">
                                    <label class="fw-bold">Cita-cita</label>
                                    <p>{{ $student->aspiration ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Orang Tua --}}
                        <div class="tab-pane fade" id="navs-pills-parents" role="tabpanel">
                            <h6 class="fw-bold text-primary mb-3"><i class="bx bx-male me-1"></i> Data Ayah / Wali</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6"><label class="small text-muted">Nama:</label><div class="fw-bold">{{ $student->guardian_name ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">Pekerjaan:</label><div>{{ $student->guardian_occupation ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">Pendidikan:</label><div>{{ $student->guardian_education ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">No. Telepon:</label><div>{{ $student->guardian_phone ?? '-' }}</div></div>
                            </div>

                            <hr>

                            <h6 class="fw-bold text-pink mb-3 mt-3" style="color: #e83e8c"><i class="bx bx-female me-1"></i> Data Ibu</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="small text-muted">Nama:</label><div class="fw-bold">{{ $student->mother_name ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">Pekerjaan:</label><div>{{ $student->mother_occupation ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">Pendidikan:</label><div>{{ $student->mother_education ?? '-' }}</div></div>
                                <div class="col-md-6"><label class="small text-muted">No. Telepon:</label><div>{{ $student->mother_phone ?? '-' }}</div></div>
                            </div>
                        </div>

                        {{-- Tab 3: Alamat --}}
                        <div class="tab-pane fade" id="navs-pills-address" role="tabpanel">
                            <p class="mb-4">
                                <i class="bx bx-map text-danger me-2 fs-4"></i> 
                                <strong>Alamat Lengkap:</strong>
                            </p>
                            <div class="alert alert-secondary">
                                {{ $student->address ?? 'Alamat belum diisi.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection