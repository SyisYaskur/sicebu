@extends('layouts.admin.app')
@section('title', 'Edit Kelas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Kelas /</span> Edit Kelas</h4>

    @include('components.alert')

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Kelas</h5>
                </div>
                <div class="card-body">

    {{-- Form Update Data Kelas --}}
    <form action="{{ route('superadmin.classes.update', $class->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Kelas --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Kelas (Angka/Huruf)</label>
            <input 
                type="text" 
                class="form-control" 
                name="name" 
                value="{{ old('name', $class->name) }}" 
                required
            >
        </div>

        {{-- Tingkat + Tahun Akademik --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tingkat</label>
                <select class="form-select" name="academic_level" required>
                    @foreach([10, 11, 12, 13] as $level)
                        <option value="{{ $level }}" {{ $class->academic_level == $level ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Tahun Akademik</label>
                <input 
                    type="text" 
                    class="form-control" 
                    name="academic_year" 
                    value="{{ old('academic_year', $class->academic_year) }}" 
                    required
                >
            </div>
        </div>

        {{-- Wali Kelas --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Wali Kelas (Guru)</label>
            <select class="form-select select2" id="user_id" name="user_id">
                <option value="">-- Pilih Guru Wali Kelas --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        {{ old('user_id', $currentTeacherId) == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Program + Konsentrasi --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Program Keahlian</label>
                <select 
                    class="form-select @error('expertise_program_id') is-invalid @enderror" 
                    id="expertise_program_id" 
                    name="expertise_program_id"
                >
                    <option value="">-- Pilih Program --</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" 
                            {{ old('expertise_program_id', $class->expertise_program_id) == $program->id ? 'selected' : '' }}>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
                @error('expertise_program_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Konsentrasi Keahlian</label>
                <select 
                    class="form-select @error('expertise_concentration_id') is-invalid @enderror" 
                    id="expertise_concentration_id" 
                    name="expertise_concentration_id"
                >
                    <option value="">-- Pilih Konsentrasi --</option>
                    @foreach ($concentrations as $concentration)
                        <option value="{{ $concentration->id }}" 
                            {{ old('expertise_concentration_id', $class->expertise_concentration_id) == $concentration->id ? 'selected' : '' }}>
                            {{ $concentration->name }}
                        </option>
                    @endforeach
                </select>
                @error('expertise_concentration_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="d-flex gap-2 mt-4">
                            {{-- Tombol Simpan --}}
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>

                            {{-- Tombol Batal / Kembali (Logic: Kembali ke halaman sebelumnya) --}}
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
        </div>

    </form>

</div>

            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Siswa ({{ count($assignedStudentIds) }})</h5>
                </div>
                
                <div class="card-body border-bottom">
                    {{-- Form 2: Tambah Siswa (POST ke route khusus) --}}
                    <form action="{{ route('superadmin.classes.addStudent', $class->id) }}" method="POST" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-9">
                            <label class="form-label">Tambah Siswa</label>
                            <select class="form-select select2" name="student_id" required>
                                <option value="">-- Cari Siswa (Nama / NIS) --</option>
                                @foreach($allStudents as $student)
                                    {{-- Hanya tampilkan siswa yang BELUM ada di kelas ini --}}
                                    @if(!in_array($student->id, $assignedStudentIds))
                                        <option value="{{ $student->id }}">
                                            {{ $student->full_name }} ({{ $student->student_number }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bx bx-plus"></i> Tambah
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>Gender</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $students = $class->studentsForThisYear()->orderBy('full_name')->get();
                            @endphp
                            
                            @forelse($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->student_number }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->gender }}</td>
                                <td class="text-center">
                                    {{-- Form 3: Hapus Siswa (DELETE ke route khusus) --}}
                                    <form action="{{ route('superadmin.classes.removeStudent', ['class' => $class->id, 'student' => $student->id]) }}" method="POST" onsubmit="return confirm('Keluarkan siswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada siswa di kelas ini. Silakan tambahkan lewat form di atas.
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush