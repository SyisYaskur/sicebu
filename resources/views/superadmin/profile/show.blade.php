@extends('layouts.admin.app') {{-- Layout Admin --}}

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Profil Saya</h1>

    @include('components.alert')

    <div class="row mt-4">
        <div class="col-xl-4">
            <div class="card mb-4 mb-xl-0">
                <div class="card-header">Foto Profil</div>
                <div class="card-body text-center">
                    <form action="{{ route('superadmin.profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            @if(Auth::user()->avatar)
                                <img class="img-account-profile rounded-circle mb-2" src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img class="img-account-profile rounded-circle mb-2" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff&size=150" alt="Avatar">
                            @endif
                        </div>

                        <div class="small font-italic text-muted mb-4">JPG atau PNG tidak lebih dari 2 MB</div>

                        <label class="btn btn-primary" for="avatar">
                            Upload gambar baru
                            <input type="file" id="avatar" name="avatar" hidden accept="image/png, image/jpeg" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">Detail Akun</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="small mb-1" for="name">Nama Lengkap</label>
                            <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1" for="email">Email</label>
                            <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Ganti Password</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.profile.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="small mb-1" for="current_password">Password Saat Ini</label>
                            <input class="form-control" id="current_password" name="current_password" type="password" required>
                        </div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label class="small mb-1" for="password">Password Baru</label>
                                <input class="form-control" id="password" name="password" type="password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small mb-1" for="password_confirmation">Konfirmasi Password</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection