@extends('layouts.teacher.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-2">Dashboard Wali Kelas</h4>

    <div class="alert alert-warning" role="alert">
      <h4 class="alert-heading"><i class="bx bx-error-alt me-1"></i> Akun Belum Ditugaskan</h4>
      <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>.</p>
      <hr>
      <p class="mb-0">Saat ini Anda belum ditugaskan sebagai Wali Kelas untuk tahun ajaran aktif. Silakan hubungi Super Admin atau Kurikulum untuk mendaftarkan Anda sebagai Wali Kelas.</p>
    </div>
</div>
@endsection