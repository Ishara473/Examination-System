@extends('layouts.student-login')

@section('content')
<div class="row justify-content-center">
    @if(Session::has('message'))
        <div class="col-md-8"><p class="alert {{ Session::get('alert-class') }}">{!! Session::get('message') !!}</p></div>
    @endif
    <div class="col-md-4">
        <div class="card mt-3">
            <div class="card-header text-center">
                <img src="{{ asset('images/logo.png') }}" class="mx-auto d-block pull-right" height="80px" />
                <h4>Faculty of Agriculture <br/> Rajarata University of Sri Lanka</h4>
                <span class="text-muted">Student Portal</span>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-error-message">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form" method="POST" action="{{ route('login') }}" id="loginForm">
                    {{ csrf_field() }}
                    <div class="form-group row{{ $errors->has('registration') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 col-form-label text-right">Registration No<span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input id="registration" type="text" class="form-control" name="registration" value="{{ old('registration') }}" required autofocus data-placement="right">
                        </div>
                    </div>
                    <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 col-form-label text-right">Password <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input id="password" type="password" class="form-control" name="password" required data-placement="right">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4"></div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('custom-js')
<script src="{{ asset('plugins/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery-validate.bootstrap-tooltip.min.js') }}"></script>
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#loginForm').validate();
});
</script>
@endsection