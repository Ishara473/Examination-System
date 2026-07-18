@extends('layouts.student')

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<h2 class="mb-1 text-grey">Annual Registration</h2>
<hr class="mt-0 mb-5"/>

{{ Form::open(['url'=>'/student/update-profile','method'=>'post']) }} 
<div class="group-section">
    <h4 class="text-primary">Registration Information</h4>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                    {!!Form::label('academic_year', 'Academic Year')!!}
                    {!! Form::text('academic_year', settings('reg_year'), ['class'=>'form-control','id'=>'academic_year','readonly'=>'readonly']) !!} 
            </div>  
        </div>
        <div class="col-md-2">
            <div class="form-group">
                    {!!Form::label('registered_year', 'Applying Year')!!}
                    {!! Form::text('registered_year', $student->AcademicDetail->current_study_year+1, ['class'=>'form-control','id'=>'registered_year','readonly'=>'readonly']) !!} 
            </div>  
        </div>
        <div class="col-md-3">
            <label class="d-block">Require Hostel</label>
            <div class="custom-control custom-radio custom-control-inline ml-3">
                <input type="radio" class="custom-control-input" id="defaultInline1" name="inlineDefaultRadiosExample" value="1">
                <label class="custom-control-label" for="defaultInline1">Yes</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" id="defaultInline2" name="inlineDefaultRadiosExample" value="0">
                <label class="custom-control-label" for="defaultInline2">No</label>
            </div>
        </div>
    </div>
</div>
<div class="text-right">
    <button type="submit" class="btn btn-success">Add Request</button>
</div>
{{ Form::close() }}

@endsection

@section('custom-js')

@endsection
