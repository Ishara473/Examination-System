<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-reboot.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/iconmoon.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/sb-admin.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ mix('scss/_custom-forms.scss') }}" rel="stylesheet"> --}}

    @yield('custom-css')
</head>
<body id="page-top">  
    <div id="wrapper">  
      <div id="content-wrapper">  
        <div class="container-fluid">    
          <!-- Page Content -->
          @yield('content')  
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-wrapper -->
    </div>
    <!-- /#wrapper -->
    
    <script src="{{ asset('js/jquery-3.4.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('js/jquery.easing.min.js') }}"></script>
    {{-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> --}}
  
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('frontend/js/sb-admin.min.js') }}"></script>

    @yield('custom-js')
    
</body>
</html>