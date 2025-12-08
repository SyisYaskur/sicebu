@extends('layouts.teacher.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun /</span> Profil Saya</h4>

    @include('components.alert')

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Akun</a>
                </li>
            </ul>

            <div class="row">
                <!-- Form Upload Avatar -->
                <div class="col-md-5">
                    <div class="card mb-4">
                        <h5 class="card-header">Foto Profil</h5>
                        <div class="card-body">
                            <form action="{{ route('walikelas.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (session('status') === 'avatar-updated')
                                    <div class="alert alert-success" role="alert">Foto profil berhasil diperbarui.</div>
                                @endif

                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="Foto Profil" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=100" alt="Foto Profil" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                    @endif

                                    <div class="button-wrapper">
                                        <label for="avatar" class="btn btn-primary me-2 mb-2" tabindex="0">
                                            <span class="d-none d-sm-block">Upload foto baru</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" id="avatar" name="avatar" class="account-file-input @error('avatar') is-invalid @enderror" hidden accept="image/png, image/jpeg" onchange="document.getElementById('form-avatar-submit').click();" />
                                        </label>
                                        <p class="text-muted mb-0">Hanya JPG atau PNG. Maks 2MB.</p>
                                        @error('avatar')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror

                                        {{-- Tombol submit tersembunyi --}}
                                        <button type="submit" id="form-avatar-submit" class="d-none">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Form Ubah Password -->
                    <div class="card">
                        <h5 class="card-header">Ubah Password</h5>
                        <div class="card-body">
                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success" role="alert">Password berhasil diubah.</div>
                            @endif

                            <form id="formAccountSettings" method="POST" action="{{ route('walikelas.profile.password') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-md-12 form-password-toggle">
                                        <label class="form-label" for="current_password">Password Saat Ini</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control @error('current_password') is-invalid @enderror" type="password" name="current_password" id="current_password" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                        @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="password">Password Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                         @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3 col-md-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required />
                                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <button type="submit" class="btn btn-primary">Simpan Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Form Detail Akun & Kepegawaian -->
                <div class="col-md-7">
                     <div class="card mb-4">
                        <h5 class="card-header">Detail Profil</h5>
                        <div class="card-body">
                            @if (session('status') === 'profile-updated')
                                <div class="alert alert-success" role="alert">Detail profil berhasil diperbarui.</div>
                            @endif

                            <form id="formAccountDetails" method="POST" action="{{ route('walikelas.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <hr class="my-3">
                                    <h6 class="text-muted">Data Kepegawaian (Read-Only)</h6>
                                    <p class="text-muted small">Data berikut tidak dapat diubah di halaman ini. Silakan hubungi Super Admin jika ada kesalahan data.</p>

                                    @if($user->employee)
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Role</label>
                                            <input class="form-control" type="text" value="{{ $user->roles->first()->name ?? 'N/A' }}" readonly disabled/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">NIP</label>
                                            <input class="form-control" type="text" value="{{ $user->employee->nip ?? 'Belum diisi' }}" readonly disabled/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">NUPTK</label>
                                            <input class="form-control" type="text" value="{{ $user->employee->nuptk_number ?? 'Belum diisi' }}" readonly disabled/>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Nomor Telepon</label>
                                            <input class="form-control" type="text" value="{{ $user->employee->phone ?? 'Belum diisi' }}" readonly disabled/>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <p class="text-warning">Data kepegawaian tidak ditemukan.</p>
                                        </div>
                                    @endif

                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan (Nama/Email)</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk preview gambar avatar --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const avatarInput = document.getElementById('avatar');
        const uploadedAvatar = document.getElementById('uploadedAvatar');

        if (avatarInput) {
            avatarInput.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        uploadedAvatar.src = event.target.result;
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
        }
    });
</script>
@endpush