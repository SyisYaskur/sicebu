@extends('layouts.admin.app')
@section('title', 'Master Siswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Master Siswa</h4>

    @include('components.alert')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Siswa</h5>
            <a href="{{ route('superadmin.students.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Tambah Siswa
            </a>
        </div>

        <div class="card-body border-bottom">
            <form action="{{ route('superadmin.students.index') }}" method="GET">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama, NIS, NISN, atau NIK..." value="{{ $search }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">NIS / NISN</th>
                        <th>Nama Lengkap</th>
                        <th width="10%">L/P</th>
                        <th>Tempat Lahir</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <span class="fw-bold d-block">{{ $student->student_number ?? '-' }}</span>
                            <small class="text-muted">{{ $student->national_student_number ?? '-' }}</small>
                        </td>
                        <td><strong>{{ $student->full_name }}</strong></td>
                        <td>{{ $student->gender == 'Laki-Laki' ? 'L' : 'P' }}</td>
                        <td>
                            {{ Str::before($student->birth_place_date, ',') }}
                        </td>
                        <td class="text-center">
                            {{-- Tombol Lihat Detail --}}
                            <a href="{{ route('superadmin.students.show', $student->id) }}" class="btn btn-sm btn-icon btn-info" title="Lihat Detail">
                                <i class="bx bx-show"></i>
                            </a>

                            {{-- Tombol Edit --}}
                            <a href="{{ route('superadmin.students.edit', $student->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                <i class="bx bx-edit-alt"></i>
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('superadmin.students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data siswa {{ $student->full_name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bx bx-search fs-1 text-muted mb-2"></i>
                            <p class="mb-0 text-muted">Data siswa tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection