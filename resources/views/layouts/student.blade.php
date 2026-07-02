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
    {{-- <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/bootstrap-reboot.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/bootstrap-grid.min.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('css/iconmoon.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('frontend/sb-admin.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/frontend.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome/css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome/css/solid.min.css') }}" rel="stylesheet">

    @yield('custom-css')
</head>
<body id="page-top">

    <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
      <img src="{{ asset('images/logo.png') }}" class=" mr-2" height="40px" />
      <a class="navbar-brand mr-1" href="/">RJT Student Portal</a>
  
      {{-- <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
      </button> --}}
  
      <!-- Navbar Search -->
      <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">

      </form>
  
      <!-- Navbar -->
      <ul class="navbar-nav ml-auto ml-md-0">
        {{-- <li class="nav-item dropdown no-arrow">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle fa-fw"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#">Settings</a>
            <a class="dropdown-item" href="#">Activity Log</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
          </div>
        </li> --}}
        <li class="nav-item">
          <a class="nav-link" href="/logout" class="btn btn-default"><i class="fa fa-power-off"></i> </a>
        </li>
      </ul>
  
    </nav>
  
    <div id="wrapper"> 
      <div id="content-wrapper">  
        <div class="container-fluid">  
          @yield('title')
          <div class="row">
            <div class="col-md-2">
              <div class=" text-center">
                  <img class="profile-user-img img-fluid img-thumbnail rounded-circle" src="{{url('/student/profile-picture/'.Auth::user()->id)}}" alt="User profile picture">
                  <h4 class="profile-username text-center mb-0">{{Auth::user()->initials}} {{Auth::user()->name_marking}}</h4>
                  <span class="text-muted">{{Auth::user()->registration_no}}</span>
                  <hr/>
              </div>
              <a class="btn btn-block btn-info text-left" href="{{url('/student/update-profile')}}"><i class="fa fa-graduation-cap"></i> Update Profile</a>
                          
              @if(Auth::user()->is_profile_confirmed==1) 
                  @if(settings('std_show_results')==1)
                  <a class="btn btn-block btn-info text-left" href="{{url('/student/view-results')}}"><i class="fa fa-file-alt"></i> Exam Results</a>
                  <div class="mb-3"></div>
                  @endif
                  {{-- @if(settings('enable_year_reg')==1)
                  <a class="btn btn-block btn-danger text-left" href="{{url('/student/annual-registration')}}"><i class="fa fa-calendar-plus"></i> Year Registration</a>
                  @endif --}}

                  <?php $maxSemester = Auth::user()->AcademicDetail->current_study_year*2; ?>
                  @if(settings('enable_semester_reg')==1 && settings('sp_select_semster') <= $maxSemester && settings('sem_reg_min_semester') <= $maxSemester)
                  <a class="btn btn-block btn-danger text-left" href="{{url('/student/semester-registration')}}"><i class="fa fa-calendar-alt"></i> Semester Registration</a>
                  @endif
                  @if(settings('sp_selection_enable')==1 && settings('sp_select_semster') <= (Auth::user()->AcademicDetail->current_study_year*2) && Auth::user()->AcademicDetail->specialization_id == 0)
                  <a class="btn btn-block btn-danger text-left" href="{{url('/student/specialization-selection')}}"><i class="fa fa-calendar-alt"></i> Apply for Specilization</a>
                  @endif
                  @if(settings('enable_exam_reg')==1 || settings('exam_app_download')==1)
                  <a class="btn btn-block btn-danger text-left" href="{{url('/student/exam-registration')}}"><i class="fa fa-pencil-alt"></i> Exam Applications</a>
                  @endif
                  {{-- @if(settings('enable_exam_reg')==1)
                  <a class="btn btn-block btn-danger text-left" href="#"><i class="fa fa-file-medical"></i> Exam Medicals</a>
                  @endif --}}
                  
                  <a class="btn btn-block btn-warning text-left" href="{{url('/student/update-password')}}"><i class="fa fa-pencil-alt"></i> Change Password</a>
              @endif
          </div>
            
          <div class="col-md-10 mb-3">
          @if(session('success'))               
              <div class="row m-1">
                  <div class="col-md-12 alert alert-success">
                      {{session('success')}}
                  </div>
              </div>
          @elseif(session('error'))
            <div class="row m-1">
                <div class="col-md-12 alert alert-danger">
                    {{session('error')}}
                </div>
            </div>
          @endif
          
          @yield('content')
          </div>
      </div>
         
        </div>
        <!-- /.container-fluid -->
  
        <!-- Sticky Footer -->
        <footer class="sticky-footer">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>Copyright © Faculty of Agriculture, Rajarata University of Sri Lanka</span>
            </div>
          </div>
        </footer>  
      </div>
      <!-- /.content-wrapper -->
  
    </div>
    <!-- /#wrapper -->
  
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>
  
  
    <script src="{{ asset('js/jquery-3.4.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('js/jquery.easing.min.js') }}"></script>
    {{-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> --}}
  
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('frontend/js/sb-admin.min.js') }}"></script>
  
   <script>
     
   </script>

    @yield('custom-js')
    
</body>
</html>