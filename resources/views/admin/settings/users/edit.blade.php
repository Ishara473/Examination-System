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
        {{ Form::open(['url' => '/admin/settings/update-user/', 'id'=>'editRecordFrom']) }}
        <input type="hidden" name="id" value="{{$user->id}}"/>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FirstName">First Name</label>
                            <input type="input" class="form-control" id="FirstName" name="first_name" required value="{{$user->first_name}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="LastName">Last Name</label>
                            <input type="input" class="form-control" id="LastName" name="last_name" required value="{{$user->last_name}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" class="form-control" id="Email" name="email" required value="{{$user->email}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Designation">Designation</label>
                            <input type="input" class="form-control" id="Designation" name="designation" required value="{{$user->designation}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Password">Password</label>
                            <input type="password" class="form-control" id="Password" name="password" >
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="d-block">&nbsp;</label>
                            <label for="IsActive">
                                <input type="checkbox" id="IsActive" name="is_active" @if($user->is_active==1) checked @endif> Is Active
                            </label>
                        </div>
                    </div>             
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-grey-overlay">Update User</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                    @foreach($roles as $role)
                        <tr>
                            <td width="10px"><input type="checkbox" value="{{$role->id}}" name="roles[{{$role->id}}]" @if(isset($user_roles[$role->id])) checked @endif></td>
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
    var validater = $('#editRecordFrom').validate();
});
</script>
@endsection