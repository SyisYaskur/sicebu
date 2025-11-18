 @extends('layouts.teacher.app')
    @section('title', 'Pengeluaran Kelas')
    @section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Kelas Saya /</span> Pengeluaran</h4>
        <div class="alert alert-warning" role="alert">
          <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Belum Ada Kelas</h4>
          <p>Anda tidak dapat mengelola pengeluaran karena Anda belum ditugaskan sebagai wali kelas.</p>
          <hr>
          <p class="mb-0">Silakan hubungi Super Admin atau bagian Kurikulum.</p>
        </div>
    </div>
    @endsection