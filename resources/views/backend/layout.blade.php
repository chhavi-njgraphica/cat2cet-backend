<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Cat Calculator Admin</title>
    <!--favicon-->
    <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">

    <!--plugins-->
    <link href="{{asset('assets/backend/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/backend/plugins/metismenu/metisMenu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/backend/plugins/metismenu/mm-vertical.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/backend/plugins/simplebar/css/simplebar.css')}}">
    <!--bootstrap css-->
    <link href="{{asset('assets/backend/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    {{-- <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"> --}}
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
	  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <!--main css-->
    <link href="{{asset('assets/backend/css/bootstrap-extended.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/sass/main.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/sass/dark-theme.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/sass/semi-dark.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/sass/bordered-theme.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/sass/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/css/flatpickr.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/backend/css/extra-icons.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/backend/plugins/notifications/css/lobibox.min.css')}}">
    <script src="https://cdn.tiny.cloud/1/v0plwjbhkupcq29dv51ij6lxr0imsbosmsnl5g5zjr3js1r7/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
      .nextMonthDay{
        color: unset !important;
      }
      .flatpickr-disabled{
        color: rgba(57, 57, 57, 0.3) !important;
      }
      .book-filter-btn{
        display: flex;
        justify-content: end;
        gap: 10px;
        margin-bottom: 20px;
      }
    </style>
</head>

<body>
    @include('backend.includes.header')
    @include('backend.includes.sidebar')
    <main class="main-wrapper">
      <div class="main-content">
        <div class="container">
        @yield('content')
      </div>
      </div>
    </main>
    <footer class="page-footer">
        <p class="mb-0 mx-5">Product developer by <a href="https://njgraphica.com/" target="_blank">Nj Graphica</a></p>
        
        <p class="mb-0 mx-5">Copyright © 2025. All right reserved.</p>
      </div>
    </footer>

    <script src="{{asset('assets/backend/js/bootstrap.bundle.min.js')}}"></script>

    <!--plugins-->
    <script src="{{asset('assets/backend/js/jquery.min.js')}}"></script>
    <!--plugins-->
    <script src="{{asset('assets/backend/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
	  <script src="{{asset('assets/backend/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/simplebar/js/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/backend/js/flatpickr.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/notifications/js/lobibox.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/notifications/js/notifications.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="{{asset('assets/backend/js/main.js')}}"></script>
    <script>
      $('.single-select-field').select2({
        theme: "bootstrap-5",
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
      });
      function success_noti(message) {
          Lobibox.notify('success', {
              pauseDelayOnHover: true,
              continueDelayOnInactiveTab: false,
              position: 'top right',
              icon: 'bi bi-check2-circle',
              msg: message // Use the dynamic message here
          });
      }

      function error_noti(message) {
        Lobibox.notify('error', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          icon: 'bi bi-x-circle',
          msg: message
        });
      }
      @if (session('success'))
        success_noti('{{ session('success') }}');
      @endif

      @if (session('error'))
        error_noti('{{ session('error') }}');
      @endif
    </script>
    @yield('script')
</body>
</html>
