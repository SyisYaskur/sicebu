<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets/') }}/" {{-- Sesuaikan path asset jika perlu --}}
  data-template="vertical-menu-template-free"
  data-style="light">
  <head>
    <meta charset="utf-cm" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', 'Dashboard Wali Kelas') - {{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" /> {{-- Sesuaikan path asset jika perlu --}}

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    {{-- Font Awesome untuk ikon Bootstrap yang saya gunakan --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" /> {{-- Sesuaikan path asset jika perlu --}}

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    
    {{-- CSS Kustom Bootstrap 5 jika diperlukan (jika asset tidak ada) --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    
    @stack('styles') {{-- Untuk CSS tambahan per halaman --}}
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        
        @include('layouts.teacher.sidebar')
        <div class="layout-page">
          
          @include('layouts.teacher.topbar')
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                @yield('content')
            </div>
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  , dibuat dengan sungguh sungguh oleh Syis YK
                </div>
              </div>
            </footer>
            <div class="content-backdrop fade"></div>
          </div>
          </div>
        </div>

      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <script src="{{  asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{  asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{  asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{  asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{  asset('assets/vendor/js/menu.js') }}"></script>
    
    {{-- JS Bawaan Bootstrap 5 (jika asset tidak ada) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script src="{{  asset('assets/js/main.js') }}"></script>

    @stack('scripts') {{-- Untuk JS tambahan per halaman --}}
  </body>
</html>