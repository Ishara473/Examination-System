@extends('layouts.admin')

@section('title')
Students
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/student">Student management</a></li>
<li class="breadcrumb-item">View</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection


@section('content')

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-1  border-left-danger">
                    <div class="card-body text-center">
                    <img class="profile-user-img img-fluid img-thumbnail rounded-circle" src="{{url('/admin/student/profile-pic')}}?type=1&id={{$student->id}}" alt="User profile picture">
                        <h4 class="profile-username text-center">{{$student->initials}} {{$student->name_marking}}</h4>
                        <span class="text-muted">{{$student->registration_no}} <br/> {{$student->AcademicDetail->BatchCode()}} <br/>{{$student->id_no}} </span>
                        <hr/>
                        <span class="d-block"><i class="fa fa-mobile-alt"></i> {{$student->contact->mobile}}</span>
                        <span class="d-block"><i class="fa fa-phone"></i> {{$student->contact->phone}}</span>
                        <span class="d-block"><i class="fa fa-envelope-o"></i> {{$student->contact->email}}</span>
                        {{-- @if(!empty($application->student))
                        <div class="text-center"><a href="#" class="text-blue"><i class="fa fa-link"></i> <b>Student Profile</a></b></div>
                        @else
                            <div class="text-center"><a href="#" class="btn btn-success"><i class="fa fa-rocket"></i> <b>Process Application</a></b></div>
                        @endif --}}
                        @if($hasDoc)
                            <a class="d-block" href="{{ url('/admin/student/get-document/'.$student->id)}}">application.pdf</a>
                        @endif
                        <img class="profile-user-img img-fluid img-thumbnail" src="{{url('/admin/student/profile-pic')}}?type=2&id={{$student->id}}" alt="User profile picture">
                    </div>
                </div>
                <div class="card border-left-danger">
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-link active"><a href="#academic-profile" data-toggle="tab"><i class="fa fa-user-graduate"></i> Academic Profile</a></li>
                            <li class="nav-link"><a href="#profile" data-toggle="tab"><i class="fa fa-user"></i> Personal Profile</a></li>
                            <li class="nav-link"><a href="#al-details" data-toggle="tab"><i class="fa fa-pencil-ruler"></i> A/L Information</a></li>
                            <li class="nav-link"><a href="#academic-results" data-toggle="tab"><i class="fa fa-university"></i> Exam results</a></li>
                            <li class="nav-link"><a href="#payments" data-toggle="tab"><i class="fa fa-dollar-sign"></i> Payments</a></li>
                            <li class="nav-link"><a href="#other" data-toggle="tab"><i class="fa fa-info-circle"></i> Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>        
            <div class="col-md-9">
                <div class="card mb-1 text-bold">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                Status : <em class="text-danger"> {{ $student->AcademicDetail->Status()}}</em>
                            </div>
                            <div class="col-md-3">
                                Academic Year :<em class="text-danger"> {{ $student->AcademicDetail->current_study_year}}</em>
                            </div>                        
                            {{-- <div class="col-md-4">
                                Department :<em class="text-danger"> @if($student->AcademicDetail->specialization_id > 0) {{$student->AcademicDetail->Specialization->department}}  @else - @endif</em>
                            </div> --}}
                            <div class="col-md-6">
                                Specialization :<em class="text-danger">@if($student->AcademicDetail->specialization_id > 0) {{$student->AcademicDetail->Specialization->name}}  @else - @endif</em>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nav-tabs-custom mb-5">
                    <div class="card">
                        <div class="card-body tab-content mb-3">
                            <div class="active tab-pane" id="academic-profile">
                                @include('/admin/students/_partials/academic')
                             </div>
                            <div class="tab-pane" id="profile">
                                @include('/admin/students/_partials/profile')
                            </div>
                            <div class="tab-pane" id="al-details">
                                @include('/admin/students/_partials/al')
                            </div>
                            <div class="tab-pane" id="academic-results">
                                @include('/admin/students/_partials/results')
                            </div>
                            <div class="tab-pane" id="payments">
                                @include('/admin/students/_partials/payments')
                            </div>
                            <div class="tab-pane" id="other">
                                <h5 class="alert bg-gray-700 text-white">Student Settings</h5>
                                <div class="row">
                                    @if($student->AcademicDetail->is_batch_miss==0)
                                    <div class="col-lg-3 col-md-3">
                                        <a class="btn btn-primary text-white d-block" id="lnkAddBatchMis"><span class="text">Add Batch-Mis</span></a>
                                    </div>
                                    @endif
                                </div>
                            </div><!-- /.tab-pane -->
                        </div>                        
                    </div><!-- /.tab-content -->
                </div><!-- /.nav-tabs-custom -->            
            </div>
        </div>
@endsection



@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script src="{{ asset('plugins/sweetalert/sweetalert2.all.min.js') }}"></script>


<script>
    function loadUplodedData(){}
   
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format:'yyyy-mm-dd'
        });
        $('#results-tab a:first').tab('show');

        $('body').on('click','#lnkAddBatchMis',function(e){
            e.preventDefault();
            $('#addBatchMisFrom')[0].reset();
            $('#addBatchMisModel').modal('show');
        });

        var validater = $('#addBatchMisFrom').validate();

        $('body').on('click','#BtnAddBatchMisFormSubmit',function(e){
            e.preventDefault();
            if($('#addBatchMisFrom').valid()){
                $.post("{{url('/admin/student/add-batch-mis')}}",$('#addBatchMisFrom').serialize(),function(data){
                    if(data==1){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Record was added',
                            showConfirmButton: false,
                            timer: 3000,
                            width: 250,
                            toast:true,
                        });
                        window.location.reload(true);
              
                    }
                });
            }
        });

        $('body').on('click','#addAchievment',function(e){
            e.preventDefault();
            $('#addAchievemntFrom')[0].reset();
            $('#addAchievemntModel').modal('show');
        });

        var achvalidater = $('#addAchievemntFrom').validate();

        $('body').on('click','#BtnAddAchFormSubmit',function(e){
            e.preventDefault();
            if($('#addAchievemntFrom').valid()){
                $.post("{{url('/admin/student/add-student-achievemnt')}}",$('#addAchievemntFrom').serialize(),function(data){
                    if(data==1){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Record was added',
                            showConfirmButton: false,
                            timer: 3000,
                            width: 250,
                            toast:true,
                        });
                        window.location.reload(true);
              
                    }
                });
            }
        });
        

    });

</script>
@endsection



@section('modal')
<div class="modal fade" id="addBatchMisModel" role="dialog" aria-labelledby="addBatchMisTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBatchMisTitle">Add Batch Mis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => url('/admin/student/add-batch-mis'), 'id'=>'addBatchMisFrom']) }}
                <input type="hidden" name="student_id" value="{{$student->id}}" />
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            {!!Form::label('newBatchCode', 'New Batch')!!}
                            {!!Form::select('newBatch', $batches ,'', ['class' => 'form-control','id'=>'newBatchCode','required'=>'required'])!!}  
                        </div>  
                    </div>
                    <div class="col-md-8 col-lg-8">
                        <div class="form-group">
                            {!!Form::label('newBatchReason', 'Reason')!!}
                            {!! Form::text('newBatchReason','', ['class'=>'form-control','id'=>'newBatchReason','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnAddBatchMisFormSubmit">Add Record</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addAchievemntModel" role="dialog" aria-labelledby="addAchievemntTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAchievemntTitle">Add Achievemnt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => url('/admin/student/add-student-achievemnt'), 'id'=>'addAchievemntFrom']) }}
                <input type="hidden" name="student_id" value="{{$student->id}}" />
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            {!!Form::label('AchType', 'New Batch')!!}
                            {!!Form::select('Type', [1=>'Achievment',0=>'Penalty'] ,'', ['class' => 'form-control','id'=>'AchType','required'=>'required'])!!}  
                        </div>  
                    </div>
                    <div class="col-md-8 col-lg-8">
                        <div class="form-group">
                            {!!Form::label('AchDetail', 'Detail')!!}
                            {!! Form::text('Comment','', ['class'=>'form-control','id'=>'AchDetail','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnAddAchFormSubmit">Add Record</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>
@endsection