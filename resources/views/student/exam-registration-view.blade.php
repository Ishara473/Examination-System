@extends('layouts.student')

@section('custom-css')

@endsection

@section('content')
<h2 class="mb-1 text-grey">Exam Registration Semester {{$semester}}</h2>
<hr class="mt-0 mb-5"/>

{{ Form::open(['url'=>'/student/exam-registration-view','method'=>'post']) }} 
@if($compulsory)
    <div class="group-section">
        <h4 class="text-primary">Compulsory Subjects</h4>
            <div class="row">
            @foreach($compulsory as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox" data-id="{{ $subject['id'] }}" id="c_select_subject_{{ $subject['id'] }}" name="compulsory[{{ $subject['id'] }}]" value="{{ $subject['id'] }}" {{$subject['registered'] == 1 ? 'checked' :''}} >
                            <label class="custom-control-label" for="c_select_subject_{{ $subject['id'] }}">{{$subject['code']}} {{$subject['name'] }}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($optional)
    <div class="group-section">
        <h4 class="text-primary">Elective Subjects</h4>
            <div class="row">
            @foreach($optional as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox elective" data-id="{{ $subject['id'] }}" id="e_select_subject_{{ $subject['id']}}" name="elective[{{ $subject['id'] }}]" value="{{ $subject['id'] }}" {{$subject['registered'] == 1 ? 'checked' :''}} >
                            <label class="custom-control-label" for="e_select_subject_{{ $subject['id'] }}">{{$subject['code']}} {{$subject['name'] }}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($audit)
    <div class="group-section">
        <h4 class="text-primary">Audit Subjects</h4>
            <div class="row">
            @foreach($audit as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox audit" data-id="{{ $subject['id'] }}" id="a_select_subject_{{ $subject['id'] }}" name="audit[{{ $subject['id'] }}]" value="{{ $subject['id'] }}" {{$subject['registered'] == 1 ? 'checked' :''}} >
                            <label class="custom-control-label" for="a_select_subject_{{ $subject['id'] }}">{{$subject['code']}} {{$subject['name'] }}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif




<div class="text-right">
    <input type="hidden" name="semester" value="{{$semester}}" />
    <input type="hidden" name="studentExamID" value="{{$studentExamID}}" />
    <button type="submit" class="btn btn-success">Register</button>
</div>
{{ Form::close() }}

@endsection

@section('custom-js')

@endsection
