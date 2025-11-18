<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .main-wrapper {
            display: flex;
            flex-grow: 1;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #343a40; /* Warna sidebar dark */
            color: #fff;
            transition: all 0.3s;
            min-height: calc(100vh - 56px); /* Tinggi viewport dikurangi tinggi topbar */
        }
        #sidebar.toggled {
            margin-left: -250px;
        }
        #content-wrapper {
            width: 100%;
            padding: 20px;
            min-height: calc(100vh - 56px - 40px); /* Tinggi viewport - topbar - footer */
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 20px;
            background: #2c3136;
        }
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
        }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: #495057;
        }
        .sidebar-nav .nav-link .fa {
            margin-right: 10px;
        }
        .footer {
            background-color: #f8f9fa; /* Warna footer light */
            padding: 10px 0;
            text-align: center;
            font-size: 0.9em;
            color: #6c757d;
            flex-shrink: 0; /* Mencegah footer mengecil */
        }
         /* Style untuk toggle button */
        #sidebarCollapse {
            color: rgba(255, 255, 255, 0.55);
            border: none;
            background: transparent;
        }
        #sidebarCollapse:hover {
            color: rgba(255, 255, 255, 0.75);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <button type="button" id="sidebarCollapse" class="btn btn-dark me-3">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name', 'Laravel') }}</a>

            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdownUser" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw"></i> {{ Auth::user()->name ?? 'Guest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="#">Profil</a></li>
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="main-wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Menu Utama</h3>
            </div>

            <ul class="nav flex-column sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{ route('superadmin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('superadmin.classes.index') }}"> {{-- Arahkan ke route index --}}
       <i class="fas fa-chalkboard-teacher"></i> Master Kelas
    </a>
    <li class="nav-item">
    <a class="nav-link" href="{{ route('superadmin.students.index') }}"> {{-- Arahkan ke route index --}}
       <i class="fas fa-user-graduate"></i> Master Siswa
    </a>
</li>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">
                       <i class="fas fa-dollar-sign"></i> Master Pemasukan
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">
                       <i class="fas fa-receipt"></i> Master Pengeluaran
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">
                       <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('superadmin.profile.show') }}">
                       <i class="fas fa-file-alt"></i> profil
                    </a>
                </li>
                <li class="nav-item">
    <a class="nav-link" href="{{ route('superadmin.users.index') }}"> {{-- Arahkan ke route index --}}
       <i class="fas fa-users"></i> Manajemen User
    </a>
</li>
                </ul>
        </nav>
        <div id="content-wrapper">
            <main>
                @yield('content')
            </main>
        </div>
        </div>

     <footer class="footer mt-auto py-3 bg-light border-top">
        <div class="container text-center">
            <span class="text-muted">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</span>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('toggled');
            });
        });
    </script>
     @stack('scripts') </body>
</html>