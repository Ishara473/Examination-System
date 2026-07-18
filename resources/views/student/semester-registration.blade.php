@extends('layouts.student')

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<h2 class="mb-1 text-grey">Semester Registration</h2>
<hr class="mt-0 mb-5"/>
{{ Form::open(['url'=>'/student/semester-registration','method'=>'post']) }} 
    @if($complusorySub)
    <div class="group-section">
        <h4 class="text-primary">Compulsory Subjects</h4>
            <div class="row">
            @foreach($complusorySub as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox" data-id="{{ $subject->id }}" id="c_select_subject_{{ $subject->id }}" name="compulsory[{{ $subject->id }}]" checked="checked" value="{{ $subject->id }}">
                            <label class="custom-control-label" for="c_select_subject_{{ $subject->id }}">{{$subject->code}} {{$subject->name }}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($optionalSub)
    <div class="group-section">
        <h4 class="text-primary">Elective Subjects</h4>
            <div class="row">
            @foreach($optionalSub as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox elective" data-id="{{ $subject->id}}" id="e_select_subject_{{ $subject->id }}" name="elective[{{ $subject->id }}]" @if($subject->type==2) checked="checked" @endif value="{{ $subject->id }}">
                            <label class="custom-control-label" for="e_select_subject_{{ $subject->id }}">{{$subject->code}} {{$subject->name }}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="group-section">
        <h4 class="text-primary">Audit Subjects</h4>
            <div class="row">
            @foreach($optionalSub as $subject)
                <div class="col-md-4">
                    <div class="custom-control custom-checkbox alert alert-secondary mb-2" style="padding-left:2.5rem">
                        <div>
                            <input type="checkbox" class="custom-control-input subject_selection_checkbox audit" data-id="{{ $subject->id }}" id="a_select_subject_{{ $subject->id}}" name="autid[{{ $subject->id }}]" @if($subject->type==0) checked="checked" @endif value="{{ $subject->id }}">
                            <label class="custom-control-label" for="a_select_subject_{{ $subject->id }}">{{$subject->code}} {{$subject->name}}</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(empty($complusorySub) && empty($optionalSub))
        <div class="alert alert-warning" role="alert">No subjects were found</div>
    @else
    <div class="text-right">
        <button type="submit" class="btn btn-success">Register</button>
    </div>
    @endif
{{ Form::close() }}  
@endsection

@section('custom-js')
<script>
    $(document).ready(function() {
       
        $('.elective').on('click',function(e){
            var id = $(this).data('id');
            if($(this).prop("checked") == true){
                $('#a_select_subject_'+id).prop('checked', false);
            }
        });
    
        $('.audit').on('click',function(e){
            var id = $(this).data('id');
            if($(this).prop("checked") == true){
                $('#e_select_subject_'+id).prop('checked', false);
            }
        });
    

    });
    </script>
@endsection
