@extends('layouts.admin.app')

@section('title', 'Master Siswa')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Master Siswa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Siswa</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-1"></i> Daftar Siswa</span>
        </div>
        <div class="card-body">

            @include('components.alert')

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('superadmin.students.index') }}" class="mb-3">
                <div class="input-group">
                    {{-- UBAH: Kembalikan placeholder --}}
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama, NIS, NISN, NIK..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                    @if($search)
                        <a href="{{ route('superadmin.students.index') }}" class="btn btn-secondary" type="button"><i class="fas fa-times"></i> Reset</a>
                    @endif
                </div>
            </form>
            <!-- Akhir Form Pencarian -->

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIS / NISN</th> {{-- UBAH: Judul Kolom --}}
                            <th>Gender</th>
                            <th>Kelas ({{ $currentAcademicYear }})</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>{{ $student->full_name }}</td>
                            {{-- UBAH: Tampilkan kedua nomor --}}
                            <td>{{ $student->student_number ?? '-' }} / {{ $student->national_student_number ?? '-' }}</td>
                            <td>{{ $student->gender ?? '-' }}</td>
                            <td>
                                {{ $student->classes->first()?->name ?? 'Belum ada kelas' }}
                            </td>
                            <td>
                                <a href="{{ route('superadmin.students.edit', $student->id) }}" class="btn btn-warning btn-sm" title="Edit Data & Kelas">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $student->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <!-- Modal Konfirmasi Hapus -->
                                @include('superadmin.students.delete_student_modal', ['student' => $student])
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center"> {{-- UBAH: colspan jadi 6 --}}
                                @if($search)
                                    Siswa dengan keyword "{{ $search }}" tidak ditemukan.
                                @else
                                    Belum ada data siswa.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <!-- Pagination -->
            <div class="mt-3">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection