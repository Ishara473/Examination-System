@extends('layouts.student')

@section('custom-css')
@endsection

@section('content')
<h2 class="mb-1 text-grey">Change Password</h2>
<hr class="mt-0 mb-5"/>


{{ Form::open(['url'=>'/student/update-password','method'=>'post', 'id'=>'formPasswordChange']) }} 

<div class="form-group row">       
    <label for="current_password" class="col-sm-2 col-form-label">Current Password :</label>
    <div class="col-sm-5">
        <input type="password" name="current_password" class="form-control" id="current_password" placeholder="Current Password" required="required">
    </div>
</div>
<div class="form-group row">  
    <label for="new_password_1" class="col-sm-2 col-form-label">New Password :</label>
    <div class="col-sm-5">
        <input type="password" name="new_password_1" class="form-control" id="new_password_1" placeholder="New Password" required="required">
    </div>
</div>
<div class="form-group row">  
    <label for="new_password_2" class="col-sm-2 col-form-label">Confirm New Password :</label>
    <div class="col-sm-5">
        <input type="password" name="new_password_2" class="form-control" id="new_password_2" placeholder="Re Enter New Password" required="required">
    </div>
</div>

<div class="text-right">
    <button type="submit" class="btn btn-success" id="btnSubmitForm">Update Password</button>
</div>
{{ Form::close() }}      
@endsection

@section('custom-js')
<script src="{{ asset('plugins/validate/jquery.validate.min.js') }}"></script>

<script>
    $(document).ready(function() {

        $('#formPasswordChange').validate({
            rules: {
                new_password_1: {
                    minlength: 5,
                },
                new_password_2: {
                    minlength: 5,
                    equalTo: "#new_password_1"
                }
            },
            errorClass: 'input-error text-danger text-small',
        });
    
    
        $('#btnSubmitForm').on('click',function(e){
            e.preventDefault();
            if($('#formPasswordChange').valid()){
                $('#formPasswordChange').submit();
            }
        });
    });
    </script>
@endsection
