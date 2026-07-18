@extends('layouts.admin')

@section('title')
Exam Application
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/exam">Exam management</a></li>
<li class="breadcrumb-item">View Application</li>
@endsection

@section('custom-css')
<style>
    .f14{font-size:14px;}
</style>
@endsection


@section('content')
        <div class="row">
            <div class="col-md-3">
                <div class="card mb-1  border-left-danger">
                    <div class="card-body text-center">
                    <img class="profile-user-img img-fluid img-thumbnail rounded-circle" src="{{url('/admin/student/profile-pic')}}?type=1&id={{$student->id}}" alt="User profile picture">
                        <h4 class="profile-username text-center">{{$student->initials}} {{$student->name_marking}}</h4>
                        <span class="text-muted">{{$student->registration_no}} <br/> {{$student->AcademicDetail->BatchCode()}} <br/> {{$regulation->name}}</span>
                        <hr/>
                        <span class="d-block"><i class="fa fa-mobile-alt"></i> {{$student->contact->mobile}}</span>
                        <span class="d-block"><i class="fa fa-phone"></i> {{$student->contact->phone}}</span>
                        <span class="d-block"><i class="fa fa-envelope-o"></i> {{$student->contact->email}}</span>
                        <img class="profile-user-img img-fluid img-thumbnail" src="{{url('/admin/student/profile-pic')}}?type=2&id={{$student->id}}" alt="User profile picture">
                    </div>
                </div>           
            </div>        
            <div class="col-md-9">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <h6>Exam Year : <em class="text-danger"> <strong>{{ $application->year}}</strong></em></h6>
                            </div>
                            <div class="col-md-3">
                                <h6>Exam Semester : <em class="text-danger"><strong>{{ $application->semester}}</strong></em></h6>
                            </div>
                            <div class="col-md-6">
                                <h6>Specialization : <em class="text-danger">@if($student->AcademicDetail->specialization_id > 0) {{$student->AcademicDetail->Specialization->name}}  @else - @endif</em></h6>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        {{-- <div class="group-section mt-5"> --}}
                            <h5 class="mt-3 mb-1 alert bg-gray-700 text-white">Compulsory Subjects</h5>
                            @if(!empty($subjects))
                            <div class="row mb-2 ml-2 f14">
                                @foreach($subjects as $subject)
                                    @if($subject->exam_type == '1')                                        
                                        @if($subject->status == 0)
                                        <div class="col-md-12 text-danger mb-2"><i class="fa fa-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @else
                                        <div class="col-md-12 text-success mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        {{-- </div> --}}
                        {{-- <div class="group-section mt-5"> --}}
                            <h5 class="mt-3 mb-1 alert bg-gray-700 text-white">Elective Subjects</h5>
                            @if(!empty($subjects))
                            <div class="row mb-2 ml-2 f14">
                                @foreach($subjects as $subject)
                                    @if($subject->exam_type == '2')
                                        @if($subject->status == 0)
                                        <div class="col-md-12 text-danger mb-2"><i class="fa fa-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @else
                                        <div class="col-md-12 text-primary mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        {{-- </div> --}}
                        {{-- <div class="group-section mt-5"> --}}
                            <h5 class="mt-3 mb-1 alert bg-gray-700 text-white">Audit Subjects</h5>
                            @if(!empty($subjects))
                            <div class="row mb-2 ml-2 f14">
                                @foreach($subjects as $subject)
                                    @if($subject->exam_type == '0')
                                        @if($subject->status == 0)
                                        <div class="col-md-12 text-danger mb-2"><i class="fa fa-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @else
                                        <div class="col-md-12 text-primary mb-2"><i class="fa fa-check-square"></i> {{$subject->code}} - {{$subject->name}}</div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        {{-- </div> --}}
                        <div class="pb-5"></div>
                    </div>
                </div>
       
            </div>
        </div>
@endsection



@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
    function loadUplodedData(){
    
        
    }
    
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format:'yyyy-mm-dd'
        });
    });
</script>

@endsection