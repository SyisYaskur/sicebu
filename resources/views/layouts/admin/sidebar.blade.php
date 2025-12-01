<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('superadmin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bold ms-2">SICEBU</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
      <a href="{{ route('superadmin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-smile"></i>
        <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <!-- Master Data -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Manajemen Data</span>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-pin"></i>
        <div class="text-truncate">Manajemen User</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.classes.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.classes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-chalkboard"></i>
        <div class="text-truncate">Manajemen Kelas</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.students.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.students.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user-voice"></i>
        <div class="text-truncate">Manajemen Siswa</div>
      </a>
    </li>

    <!-- Laporan & Intervensi -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Keuangan</span>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.incomes.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.incomes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-wallet"></i>
        <div class="text-truncate">Rekap Pemasukan</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('superadmin.expenses.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.expenses.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cart"></i>
        <div class="text-truncate">Rekap Pengeluaran</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}">
        <a href="{{ route('superadmin.reports.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bxs-report"></i>
            <div class="text-truncate">Pusat Laporan</div>
        </a>
    </li>

    <!-- Akun -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Akun</span>
    </li>
    
    <li class="menu-item {{ request()->routeIs('superadmin.profile.*') ? 'active' : '' }}">
      <a href="{{ route('superadmin.profile.show') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div class="text-truncate">Profil Saya</div>
      </a>
    </li>

    <li class="menu-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a class="menu-link" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div class="text-truncate">Logout</div>
            </a>
        </form>
    </li>
  </ul>
</aside>