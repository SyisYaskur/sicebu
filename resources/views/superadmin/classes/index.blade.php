@extends('layouts.admin.app')

@section('title', 'Master Kelas')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Master Kelas</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelas</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-table me-1"></i> Daftar Kelas</span>
            <a href="{{ route('superadmin.classes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Kelas
            </a>
        </div>
        <div class="card-body">

            @include('components.alert') {{-- Include alert component --}}

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Tingkat</th>
                            <th>Thn. Akademik</th>
                            <th>Program</th>
                            <th>Konsentrasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classes as $index => $class)
                        <tr>
                            <td>{{ $classes->firstItem() + $index }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->teacher_name ?? '-' }}</td>
                            <td>{{ $class->academic_level ?? '-' }}</td>
                            <td>{{ $class->academic_year ?? '-' }}</td>
                            <td>{{ $class->expertiseProgram?->name ?? '-' }}</td>
                            <td>{{ $class->expertiseConcentration?->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('superadmin.classes.show', $class->id) }}" class="btn btn-info btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('superadmin.classes.edit', $class->id) }}" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $class->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <div class="modal fade" id="deleteModal-{{ $class->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $class->id }}" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel-{{ $class->id }}">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        Apakah Anda yakin ingin menghapus kelas <strong>{{ $class->name }}</strong>?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('superadmin.classes.destroy', $class->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data kelas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-3">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection