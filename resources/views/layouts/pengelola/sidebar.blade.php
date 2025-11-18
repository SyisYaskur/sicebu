<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bold ms-2">Iruan Seribu</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('pengelola.dashboard') ? 'active' : '' }}">
      <a href="{{ route('pengelola.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-smile"></i>
        <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Judul Menu -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Laporan</span>
    </li>

    <!-- Laporan Pemasukan -->
    <li class="menu-item"> {{-- Tambahkan logic 'active' nanti --}}
      <a href="#" class="menu-link"> {{-- Ganti # dengan route laporan pengelola --}}
        <i class="menu-icon tf-icons bx bx-log-in-circle"></i>
        <div class="text-truncate" data-i18n="Pemasukan">Laporan Pemasukan</div>
      </a>
    </li>

    <!-- Laporan Pengeluaran -->
    <li class="menu-item"> {{-- Tambahkan logic 'active' nanti --}}
      <a href="#" class="menu-link"> {{-- Ganti # dengan route laporan pengelola --}}
        <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
        <div class="text-truncate" data-i18n="Pengeluaran">Laporan Pengeluaran</div>
      </a>
    </li>
    
    <!-- Laporan Global -->
    <li class="menu-item {{ request()->routeIs('pengelola.reports.index') ? 'active' : '' }}">
      <a href="{{ route('pengelola.reports.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bxs-report"></i>
        <div class="text-truncate" data-i18n="Laporan">Laporan Keuangan</div>
      </a>
    </li>


    <!-- Judul Menu Lain -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Akun</span>
    </li>
    
    <!-- Profil -->
    <li class="menu-item {{ request()->routeIs('pengelola.profile.show') ? 'active' : '' }}">
  <a href="{{ route('pengelola.profile.show') }}" class="menu-link"> {{-- UBAH INI --}}
    <i class="menu-icon tf-icons bx bx-user"></i>
    <div class="text-truncate" data-i18n="Profil">Profil Saya</div>
  </a>
</li>
  </ul>
</aside>
<!-- / Menu -->