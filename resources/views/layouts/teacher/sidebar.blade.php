<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-text demo menu-text fw-bold ms-2">SICEBU</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    
    <li class="menu-item {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}">
      <a href="{{ route('walikelas.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-smile"></i>
        <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Menu Wali Kelas</span>
    </li>

    <li class="menu-item {{ request()->routeIs('walikelas.my-class.show') ? 'active' : '' }}">
      <a href="{{ route('walikelas.my-class.show') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bxs-chalkboard"></i>
        <div class="text-truncate" data-i18n="Kelas Saya">Kelas Saya</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('walikelas.incomes.*') ? 'active' : '' }}">
      <a href="{{ route('walikelas.incomes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-log-in-circle"></i>
        <div class="text-truncate" data-i18n="Pemasukan">Pemasukan Kelas</div>
      </a>
    </li>

        <li class="menu-item {{ request()->routeIs('walikelas.expenses.*') ? 'active' : '' }}">
          <a href="{{ route('walikelas.expenses.index') }}" class="menu-link"> {{-- UBAH INI --}}
            <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
            <div class="text-truncate" data-i18n="Pengeluaran">Pengeluaran Kelas</div>
          </a>
        </li>

    <li class="menu-item {{ request()->routeIs('walikelas.reports.index') ? 'active' : '' }}">
  <a href="{{ route('walikelas.reports.index') }}" class="menu-link"> {{-- UBAH INI --}}
    <i class="menu-icon tf-icons bx bxs-report"></i>
    <div class="text-truncate" data-i18n="Laporan">Laporan Kelas</div>
  </a>
</li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Akun</span>
    </li>
    
    <li class="menu-item {{ request()->routeIs('walikelas.profile.show') ? 'active' : '' }}">
  <a href="{{ route('walikelas.profile.show') }}" class="menu-link"> {{-- UBAH INI --}}
    <i class="menu-icon tf-icons bx bx-user"></i>
    <div class="text-truncate" data-i18n="Profil">Profil Saya</div>
  </a>
</li>
    
    </ul>
</aside>