@extends('layouts.admin')

@section('title')
Create New User
@endsection


@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item"><a href="{{url('/admin/settings/roles')}}">Users</a></li>
<li class="breadcrumb-item">Add</li>
@endsection


@section('content')
@if($errors->all())
<div class="alert alert-warning" role="alert">
    <ul class="mb-1 mt-1">
    @foreach($errors->all() as $message)
        <li> {{$message}}</li>
    @endforeach
    </ul>
</div>

@endif
<div class="card border-left-danger">
    <div class="card-body">
        {{ Form::open(['url' => '/admin/settings/add-user/', 'id'=>'addRecordFrom']) }}
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FirstName">First Name</label>
                            <input type="input" class="form-control" id="FirstName" name="first_name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="LastName">Last Name</label>
                            <input type="input" class="form-control" id="LastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" class="form-control" id="Email" name="email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Designation">Designation</label>
                            <input type="input" class="form-control" id="Designation" name="designation" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Password">Password</label>
                            <input type="password" class="form-control" id="Password" name="password" required>
                        </div>
                    </div>              
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-grey-overlay">Create User</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                    @foreach($roles as $role)
                        <tr>
                            <td width="10px"><input type="checkbox" value="{{$role->id}}" name="roles[{{$role->id}}]"></td>
                            <td width="30%">{{$role->name}}</td>
                            <td>{{$role->description}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

        </div>
            
            
            
        </form>
    </div>
</div>
@endsection


@section('custom-js')
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {
    var validater = $('#addRecordFrom').validate();
});
</script>
@endsection