@extends('layouts.student')

@section('custom-css')

@endsection

@section('content')
<h2 class="mb-1 text-grey">Semester {{$application->semester}} Exams : Approved Subjects  </h2>
<hr class="mt-0 mb-3"/>
<div class="row">
    @if(!empty($subjects[1]))
    <div class="col-md-6">
        <h4 class="mt-3 mb-3">Compulsory Subjects</h4>
        @foreach($subjects[1] as $subject)
            <div class="row ml-3"><div class="col-md-12 text-info mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div></div>
        @endforeach
    </div>
    @endif
    @if(!empty($subjects[2]))
    <div class="col-md-6">
        <h4 class="mt-3 mb-3">Elective Subjects</h4>
        @foreach($subjects[2] as $subject)
            <div class="row ml-3"><div class="col-md-12 text-info mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div></div>
        @endforeach

    </div>
    @endif
    @if(!empty($subjects[0]))
    <div class="col-md-6">
        <h4 class="mt-3 mb-3">Audit Subjects</h4>
        @foreach($subjects[0] as $subject)
            <div class="row ml-3"><div class="col-md-12 text-info mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div></div>
        @endforeach

    </div>
    @endif
</div>

@endsection

@section('custom-js')

@endsection
