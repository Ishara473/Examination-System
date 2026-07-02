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
    <link href="{{ asset('backend/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome/css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/fontawesome/css/solid.min.css') }}" rel="stylesheet">
    @yield('custom-css')
</head>
<body id="page-top">   
    <div id="wrapper">
        <ul class="navbar-nav sidebar sidebar-dark accordion " id="accordionSidebar">            
            <a class="sidebar-brand d-flex align-items-center justify-content-center mt-1 mb-3" href="/admin">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('images/logo.png') }}" class="" height="100px" />
                </div>
            </a>
            <div class="sidebar-user">
                <div class="">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</div>
                <small style="line-height:.5;">{{Auth::user()->designation}}</small>
            </div>           
            <li class="sidebar-header">Main</li>
            @if(( Auth::user()->hasPermissionTo('student:view') || Auth::user()->hasRole('Admin')))
            <li class="nav-item {{ Request::is('admin/student*')?'active':'' }}">
                <a class="nav-link {{ Request::is('admin/student*')?'':'collapsed' }}" data-toggle="collapse" href="#" data-target="#main-nav-student" ><i data-feather="user" class="mr-1"></i><span>Student Management</span></a>
                <div class="collapse {{ Request::is('admin/student*')?'show':'' }}" id="main-nav-student">
                    <div class="collapse-inner">
                        <a class="collapse-item" href="{{url('/admin/student')}}">View Students</a>
                        @if(( Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/student/upload')}}">Upload Accounts</a>                       
                        <a class="collapse-item" href="{{url('/admin/student/upload-profile-pic')}}">Upload Profile Pictures</a>
                        <a class="collapse-item" href="{{url('/admin/student/upload-documents')}}">Upload Documents</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('student:delete') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/student/transfer')}}">Remove Transferred</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/student/graduate')}}">Update Graduated List</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/student/upload-scholarship')}}">Update Scholarships</a>
                        @endif
                    </div>
                </div>
            </li>
            @endif
            @if(( Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasPermissionTo('specialization:process') || Auth::user()->hasRole('Admin')))
            <li class="nav-item {{ Request::is('admin/registration*')?'active':'' }}">
                <a class="nav-link {{ Request::is('admin/registration*')?'':'collapsed' }}" data-toggle="collapse" href="#" data-target="#main-nav-registartion" ><i data-feather="key" class="mr-1"></i><span>Registration</span></a>
                <div class="collapse {{ Request::is('admin/registration*')?'show':'' }}" id="main-nav-registartion">
                    <div class="collapse-inner">
                        @if(Auth::user()->hasPermissionTo('registration:view') || Auth::user()->hasRole('Admin'))
                        <a class="collapse-item" href="{{url('/admin/registration/view-year-registration')}}">Year Registration</a>
                        <a class="collapse-item" href="{{url('/admin/registration/export-to-lms')}}">Export Registration to VLE</a>
                        @endif
                        @if(Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasRole('Admin'))
                        <a class="collapse-item" href="{{url('/admin/registration/upload-year-registration')}}">Upload Year Registration</a>
                        @endif
                        @if(Auth::user()->hasPermissionTo('specialization:process') || Auth::user()->hasRole('Admin'))
                        <a class="collapse-item" href="{{url('/admin/registration/process-specialization')}}">Process Specialization</a>
                        @endif
                    </div>
                </div>
            </li>
            @endif

            @if(( Auth::user()->hasPermissionTo('examapp:view') || Auth::user()->hasPermissionTo('examapp:approve') || Auth::user()->hasPermissionTo('examapp:print') ||Auth::user()->hasRole('Admin')))
            <li class="nav-item {{ Request::is('admin/exam*')?'active':'' }}">
                <a class="nav-link {{ Request::is('admin/exam*')?'':'collapsed' }}" aria-expanded="{{ Request::is('admin/exam*')?'true':'false' }}" data-toggle="collapse" href="#" data-target="#main-nav-exam" ><i data-feather="edit-2" class="mr-1"></i><span>Exam Management</span></a>
                <div class="collapse {{ Request::is('admin/exam*')?'show':'' }}" id="main-nav-exam">
                    <div class="collapse-inner">
                         @if(( Auth::user()->hasPermissionTo('examapp:view') ||  Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/exam')}}">View Applications</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('examapp:approve') || Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/exam/approve-by-subject')}}">Approve Subject Requests</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('examapp:print') || Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/exam/download-applications')}}">Print Admissions</a>
                        @endif
                        {{-- <a class="collapse-item" href="{{url('/admin/exam/medical')}}">Process Medical</a> --}}
                    </div>
                </div>
            </li>
            @endif
            
            @if(( Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin')))
            <li class="nav-item {{ Request::is('admin/results*')?'active':'' }}">
                <a class="nav-link {{ Request::is('admin/results*')?'':'collapsed' }}" aria-expanded="{{ Request::is('admin/results*')?'true':'false' }}" data-toggle="collapse" href="#" data-target="#main-nav-results" ><i data-feather="file-text" class="mr-1"></i><span>Result Management</span></a>
                <div class="collapse {{ Request::is('admin/results*')?'show':'' }}" id="main-nav-results">
                    <div class="collapse-inner">
                        @if(( Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/results/view-uploaded-results')}}">View Results</a>
                        <a class="collapse-item" href="{{url('/admin/results/upload-results')}}">Upload Results</a>
                        @endif
                        @if(( Auth::user()->hasPermissionTo('results:upload-bulk') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/results/upload-results-bulk')}}">Bulk Upload Results</a>
                        @endif
                        
                        @if(( Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasRole('Admin')))
                        <a class="collapse-item" href="{{url('/admin/results/process-gpa')}}">Process GPA</a>
                        <a class="collapse-item" href="{{url('admin/results/upload-gpa')}}">Upload Processed GPA</a>
                        @endif

                    </div>
                </div>
            </li>
            @endif
       

            @if(( Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin')))
            <li class="nav-item {{ Request::is('admin/transcripts*')?'active':'' }}">
                <a class="nav-link {{ Request::is('admin/transcripts*')?'':'collapsed' }}" aria-expanded="{{ Request::is('admin/transcripts*')?'true':'false' }}" data-toggle="collapse" href="#" data-target="#main-nav-transcripts" ><i data-feather="layers" class="mr-1"></i><span>Transcripts</span></a>
                <div class="collapse {{ Request::is('admin/transcripts*')?'show':'' }}" id="main-nav-transcripts">
                    <div class="collapse-inner">
                        <a class="collapse-item" href="{{url('/admin/transcripts/semester-transcripts')}}">Statement of Semester Results</a>
                        <a class="collapse-item" href="{{url('/admin/transcripts/final-transcripts')}}">Final Transcript</a>
                        <a class="collapse-item" href="{{url('/admin/transcripts/final-detail-certificate')}}">Detailed Results Sheet</a>

                    </div>
                </div>
            </li>
            @endif

        </ul>
        <div id="content-wrapper" class="d-flex flex-column">            
            <nav class="navbar navbar-expand navbar-light topbar static-top text-white">                    
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item ">
                        <button id="sidebarToggle" class="btn btn-default text-white"><i data-feather="menu"></i></button>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    @if(( Auth::user()->hasPermissionTo('system:manage') || Auth::user()->hasRole('Admin')))
                    <li class="nav-item ">
                        <a class="btn btn-default text-white" href="{{url('/admin/settings')}}">
                            <i data-feather="sliders"></i>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item ">
                        <a class="btn btn-default text-white" href="{{url('/admin/account')}}">
                            <i data-feather="user"></i>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="btn btn-default text-white" href="{{url('/admin/logout')}}">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </li>                 
                </ul>                
            </nav>
            <div class="content">
                <div class="row m-1 text-white">
                    <div class="col-md-6">
                        <h3>@yield('title')</h3> 
                        <nav class="" aria-label="breadcrumb"><ol class="breadcrumb">/&nbsp;@yield('breadcrumb')</ol></nav>
                    </div>
                    <div class="col-md-6 text-right">
                        @yield('controlButtons')
                    </div>
                </div>
                <div class="row m-1">
                    <div class="col-md-12">
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
                    </div>
                    <div class="col-md-12 main-content">
                        @yield('content')                      
                    </div>        
                </div>
            </div>
            <footer class="sticky-footer">
                <div class="container my-auto">
                  <div class="copyright text-center my-auto">
                    <span>Copyright © Faculty of Agriculture, Rajarata University of Sri Lanka</span>
                  </div>
                </div>
            </footer> 
        </div>
    </div>
    @yield('modal')
    <script src="{{ asset('js/jquery-3.4.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('backend/js/feather.min.js') }}"></script>
    <script>
        feather.replace()
    </script>
    @yield('custom-js')
    
</body>
</html>