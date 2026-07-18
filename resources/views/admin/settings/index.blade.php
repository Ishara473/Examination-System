@extends('layouts.admin')

@section('title')
Students
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/settings">Settings</a></li>
@endsection

@section('custom-css')
@endsection


@section('content')
<div class="card border-left-danger">
    <div class="card-body mb-5 mt-3">
        <h2 class="mb-0"><i data-feather="sliders" class="mr-1"></i> System Management</h2>
        <hr class="mt-1 mb-4">
        <div class="row">
            @if ((Auth::user()->hasPermissionTo('manage:settings') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1"> 
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/system-settings')}}"><span class="icon"><i data-feather="settings"></i></span><span class="text">System Settings</span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/roles')}}""><span class="icon"><i data-feather="user-check"></i></span><span class="text"> System Roles </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/users')}}"><span class="icon"><i data-feather="users"></i></span><span class="text"> System Users </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:fee') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/fees')}}"><span class="icon"><i data-feather="dollar-sign"></i></span><span class="text"> Fees </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:regulation') || Auth::user()->hasRole('Admin') ))
             <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/regulation')}}"><span class="icon"><i data-feather="briefcase"></i></span><span class="text"> Regulations </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:batch') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/batch')}}"><span class="icon"><i data-feather="package"></i></span><span class="text"> Batches </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/courses')}}"><span class="icon"><i data-feather="book-open"></i></span><span class="text"> Course Subjects </span></a>
            </div>
            @endif
            @if ((Auth::user()->hasPermissionTo('manage:schedule') || Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/schedules')}}"><span class="icon"><i data-feather="calendar"></i></span><span class="text"> Course Schedules</span></a>
            </div>
            @endif
            @if ((Auth::user()->hasRole('Admin') ))
            <div class="col-md-6 col-lg-3 mb-1">
                <a class="btn btn-info text-center btn-icon-split full" href="{{url('/admin/settings/system-logs')}}"><span class="icon"><i data-feather="calendar"></i></span><span class="text"> System Logs</span></a>
            </div>
            @endif         
        </div>
    </div>
</div> 
@endsection



@section('custom-js')

@endsection