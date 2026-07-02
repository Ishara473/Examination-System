@extends('layouts.student')

@section('custom-css')

@endsection

@section('content')

<h2 class="mb-1 text-grey">Exam Applications</h2>
<hr class="mt-0 mb-5"/>
@if(!empty($semesters))
<div class="group-section"> 
    <h4 class="text-grey"><i class="fa fa-pencil-alt"></i> Register for Semester Exams</h4>
    <div class="row ml-3 mr-3">
        @foreach($semesters as $semester)
            <div class="col-md-2 mb-2">
                <div>
                    <a class="btn btn-info btn-block text-center" href="{{url('/student/exam-registration-view?semester='."$semester")}}"> Semester {{ $semester }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if(!empty($applications))
<div class="group-section"> 
    <h4 class="text-grey"><i class="fa fa-search"></i> View Approved Subjects</h4>
    <div class="row ml-3 mr-3">
        @foreach($applications as $app)
            <div class="col-md-2 mb-2">
                <div>
                    <a class="btn btn-info btn-block text-center" href="{{url('/student/view-approved-exam-subjects?semester='.$app['semester'])}}"> Semester {{ $app['semester'] }}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endif

@endsection

@section('custom-js')

@endsection
