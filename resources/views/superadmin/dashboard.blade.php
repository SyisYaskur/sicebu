@extends('layouts.admin.app')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Super Admin</h4>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Pengguna</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $stats['total_users'] }}</h3>
                            </div>
                            <small>Guru, Admin, Pengelola</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-user bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Siswa</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $stats['total_students'] }}</h3>
                            </div>
                            <small>Terdata di sistem</small>
                        </div>
                        <span class="badge bg-label-info rounded p-2">
                            <i class="bx bx-group bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Kelas</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $stats['total_classes'] }}</h3>
                            </div>
                            <small>Kelas Aktif</small>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-chalkboard bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Wali Kelas Aktif</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $stats['active_homerooms'] }}</h3>
                            </div>
                            <small>Dari {{ $stats['total_classes'] }} kelas</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-id-card bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Status Sistem</h5>
        </div>
        <div class="card-body">
            <p>Sistem SICEBU berjalan dengan baik. Gunakan menu di samping untuk mengelola data master.</p>
            <a href="{{ route('superadmin.classes.index') }}" class="btn btn-primary">Kelola Kelas & Wali Kelas</a>
        </div>
    </div>
</div>
@endsection