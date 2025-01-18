<!doctype html>
<html lang="en" data-bs-theme="blue-theme">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>
  <link rel="icon" href="{{ asset('user/assets/images/favicon-32x32.png') }}" type="image/png">
  <link href="{{ asset('user/assets/css/pace.min.css') }}" rel="stylesheet">
  <script src="{{ asset('user/assets/js/pace.min.js') }}"></script>
  <link href="{{ asset('user/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link href="{{ asset('user/assets/plugins/metismenu/metisMenu.min.css') }}" rel="stylesheet">
  <link href="{{ asset('user/assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="http://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="{{ asset('user/sass/main.css') }}" rel="stylesheet">
  <link href="{{ asset('user/font/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('user/sass/dark-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('user/sass/blue-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('user/sass/semi-dark.css') }}" rel="stylesheet">
  <link href="{{ asset('user/sass/bordered-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('user/sass/responsive.css') }}" rel="stylesheet">
</head>
<body>
  @include('user.layouts.header')
  <aside class="sidebar-wrapper" data-simplebar="true">
    @include('user.layouts.sidebar')
  </aside>
  <main class="main-wrapper">
    <div class="main-content">
      @yield('content')
    </div>
  </main>
  <footer class="page-footer">
    @include('user.layouts.footer')
  </footer>
  <script src="{{ asset('user/assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('user/assets/js/jquery.min.js') }}"></script>
  {{-- <script src="{{ asset('user/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script> --}}
  <script src="{{ asset('user/assets/plugins/metismenu/metisMenu.min.js') }}"></script>
  <script src="{{ asset('user/assets/js/main.js') }}"></script>
  @yield('js') 
  @stack('scripts') 
</body>
</html>
