@extends('layouts.admin.app')
@section('title', 'Master Kelas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Master Kelas</h4>

    @include('components.alert')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kelas</h5>
            <a href="{{ route('superadmin.classes.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Tambah Kelas
            </a>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th class="text-center">Thn. Akademik</th>
                        <th>Konsentrasi Keahlian</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $index => $class)
                    <tr>
                        <td class="text-center">{{ $classes->firstItem() + $index }}</td>
                        
                        {{-- Nama Kelas Lengkap --}}
                        <td><strong>{{ $class->full_name }}</strong></td>
                        
                        {{-- Wali Kelas --}}
                        <td>{{ $class->teacher_name ?? '-' }}</td>
                        
                        {{-- Tahun Akademik --}}
                        <td class="text-center"><span class="badge bg-label-info">{{ $class->academic_year }}</span></td>
                        
                        {{-- Konsentrasi (Program tidak perlu ditampilkan di sini) --}}
                        <td>{{ $class->expertiseConcentration?->name ?? '-' }}</td>
                        
                        <td class="text-center">
                            {{-- Tombol Aksi Langsung (Lebih Cepat daripada Dropdown) --}}
                            <a href="{{ route('superadmin.classes.show', $class->id) }}" class="btn btn-sm btn-icon btn-info" title="Lihat Detail & Siswa">
                                <i class="bx bx-show"></i>
                            </a>
                            <a href="{{ route('superadmin.classes.edit', [$class->id, 'redirect' => 'index']) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                            <form action="{{ route('superadmin.classes.destroy', $class->id) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN 1: Apakah Anda yakin ingin menghapus kelas {{ $class->full_name }}?') && confirm('PERINGATAN TERAKHIR: Menghapus kelas ini akan MENGHAPUS SEMUA DATA SISWA, PEMASUKAN, DAN PENGELUARAN di dalamnya secara PERMANEN. Lanjutkan?');">
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
                        <td colspan="6" class="text-center py-4">Belum ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- INI YANG SEBELUMNYA HILANG: Tombol Pagination --}}
        <div class="card-footer">
            {{ $classes->links() }}
        </div>

    </div>
</div>
@endsection