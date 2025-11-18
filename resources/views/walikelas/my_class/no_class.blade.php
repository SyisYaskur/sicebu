@extends('layouts.teacher.app') {{-- Gunakan layout guru --}}

@section('title', 'Kelas Tidak Ditemukan') {{-- Judul halaman --}}

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Kelas Saya</h1>
    <ol class="breadcrumb mb-4">
        {{-- Breadcrumb navigasi --}}
        <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Kelas Saya</li>
    </ol>

    {{-- Pesan Peringatan --}}
    <div class="alert alert-warning" role="alert">
      <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Belum Ada Kelas</h4>
      <p>Saat ini Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran yang sedang aktif.</p>
      <hr>
      <p class="mb-0">Silakan hubungi Super Admin atau bagian Kurikulum untuk informasi lebih lanjut mengenai penugasan wali kelas.</p>
    </div>

    {{-- (Opsional) Tambahkan tombol atau link lain jika perlu --}}
    {{-- <a href="{{ route('walikelas.dashboard') }}" class="btn btn-primary">Kembali ke Dashboard</a> --}}
</div>
@endsection