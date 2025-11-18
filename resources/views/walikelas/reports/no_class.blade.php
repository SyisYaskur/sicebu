@extends('layouts.teacher.app')
@section('title', 'Laporan Kelas')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kelas Saya /</span> Laporan</h4>
    <div class="alert alert-warning" role="alert">
      <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Belum Ada Kelas</h4>
      <p>Anda tidak dapat melihat laporan karena Anda belum ditugaskan sebagai wali kelas.</p>
    </div>
</div>
@endsection