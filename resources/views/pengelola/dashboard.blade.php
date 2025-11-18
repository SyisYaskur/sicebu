@extends('layouts.pengelola.app') {{-- Gunakan layout baru --}}

@section('title', 'Dashboard Pengelola')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Pengelola Keuangan</h4>

    <!-- Pesan Selamat Datang -->
    <div class="alert alert-success" role="alert">
        <i class="bx bx-check-circle me-2"></i>
        Selamat datang, <strong>{{ Auth::user()->name }}</strong>! Anda login sebagai Pengelola.
    </div>

    <!-- Konten Khusus Pengelola -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Ringkasan Laporan</h5>
        </div>
        <div class="card-body">
            <p>Ini adalah area konten utama untuk Pengelola Keuangan.</p>
            <p>Di sini nanti kita akan menampilkan Laporan Pemasukan dan Pengeluaran dari semua kelas.</p>
            {{-- Tambahkan link ke laporan --}}
            <a href="#" class="btn btn-primary">Lihat Laporan Detail</a>
        </div>
    </div>
</div>
@endsection