<h5 class="alert bg-gray-700 text-white">Academic Information</h5>
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('batch', 'Batch')!!}
                {!! Form::text('batch', $student->AcademicDetail->batchCode(), ['class'=>'form-control','id'=>'batch','readonly'=>'readonly']) !!}              
        </div>  
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('regulation', 'Regulation')!!}
                {!! Form::text('regulation', $regulation->name, ['class'=>'form-control','id'=>'regulation','readonly'=>'readonly']) !!}              
        </div>  
    </div>    
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('registration_no', 'Registration No')!!}
                {!! Form::text('registration_no', $student->registration_no, ['class'=>'form-control','id'=>'registration_no','readonly'=>'readonly']) !!}              
        </div>  
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('index_no', 'Index No')!!}
                {!! Form::text('index_no', $student->index_no, ['class'=>'form-control','id'=>'index_no','readonly'=>'readonly']) !!}              
        </div>  
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="form-group">
                {!!Form::label('specialization_id', 'Specialization')!!}
                {!!Form::select('specialization_id', $specializations ,$student->AcademicDetail->specialization_id, ['class' => 'form-control','id'=>'specialization_id','disabled'=>'disabled'])!!}  
              
        </div>  
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('is_batch_miss', 'Is Batch Missed',['class'=>'d-block'])!!}
                <div class="custom-control custom-radio custom-control-inline">
                {!! Form::radio('is_batch_miss','0',($student->AcademicDetail->is_batch_miss==0)?true:false, ['class'=>'custom-control-input','id'=>'is_batch_miss_0', 'readonly'=>'readonly']) !!}
                {!!Form::label('is_batch_miss_0', 'No',['class'=>'custom-control-label'])!!}
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    {!! Form::radio('is_batch_miss','1',($student->AcademicDetail->is_batch_miss==1)?true:false, ['class'=>'custom-control-input','id'=>'is_batch_miss_1', 'readonly'=>'readonly']) !!}
                    {!!Form::label('is_batch_miss_1', 'Yes',['class'=>'custom-control-label'])!!}
                </div>
        </div>  
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="form-group">
                {!!Form::label('registration_date', 'Registration Date')!!}
                {!! Form::text('registration_date', $student->AcademicDetail->registration_date, ['class'=>'form-control datepicker','id'=>'registration_date','readonly'=>'readonly']) !!}              
        </div>  
    </div>
</div>
<div class="group-section">
    <h5 class="text-secondary">GPA</h5>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s1_gpa', 'Semester 1 GPA')!!}
                    {!! Form::text('s1_gpa', $student->AcademicDetail->s1_gpa, ['class'=>'form-control','id'=>'s1_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s2_gpa', 'Semester 2 GPA')!!}
                    {!! Form::text('s2_gpa', $student->AcademicDetail->s2_gpa, ['class'=>'form-control','id'=>'s2_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s3_gpa', 'Semester 3 GPA')!!}
                    {!! Form::text('s3_gpa', $student->AcademicDetail->s3_gpa, ['class'=>'form-control','id'=>'s3_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s4_gpa', 'Semester 4 GPA')!!}
                    {!! Form::text('s4_gpa', $student->AcademicDetail->s4_gpa, ['class'=>'form-control','id'=>'s4_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s5_gpa', 'Semester 5 GPA')!!}
                    {!! Form::text('s5_gpa', $student->AcademicDetail->s5_gpa, ['class'=>'form-control','id'=>'s5_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s6_gpa', 'Semester 6 GPA')!!}
                    {!! Form::text('s6_gpa', $student->AcademicDetail->s6_gpa, ['class'=>'form-control','id'=>'s6_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s7_gpa', 'Semester 7 GPA')!!}
                    {!! Form::text('s7_gpa', $student->AcademicDetail->s7_gpa, ['class'=>'form-control','id'=>'s7_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('s8_gpa', 'Semester 8 GPA')!!}
                    {!! Form::text('s8_gpa', $student->AcademicDetail->s8_gpa, ['class'=>'form-control','id'=>'s8_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('y2_gpa', 'Year 2 Cumulative GPA')!!}
                    {!! Form::text('y2_gpa', $student->AcademicDetail->y2_gpa, ['class'=>'form-control','id'=>'y2_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                    {!!Form::label('final_gpa', 'Final GPA')!!}
                    {!! Form::text('final_gpa', $student->AcademicDetail->final_gpa, ['class'=>'form-control','id'=>'final_gpa','readonly'=>'readonly']) !!}              
            </div>  
        </div>
    </div>
</div>
{{ Form::open(['url'=>url('/admin/student/update-scholarship-details'),'method'=>'post','id'=>'frmScholarship']) }}
{!! Form::hidden('id', $student->id) !!} 
<h5 class="alert bg-gray-700 text-white">Scholarship Information</h5>
<div class="row"> 
    <div class="col-lg-4 col-md-6">
        <div class="form-group">
            {!!Form::label('main_scholarship', 'Primary Scholarship',['class'=>'d-block'])!!}
            {!!Form::select('main_scholarship', $scholarships ,$student->AcademicDetail->main_scholarship, ['class' => 'form-control','id'=>'main_scholarship'])!!}  
        </div>  
    </div>
    <div class="col-lg-2 col-md-6">
        <div class="form-group">
                {!!Form::label('scholarship_start_date', 'Awarded From')!!}
                {!! Form::text('scholarship_start_date', $student->AcademicDetail->scholarship_start_date, ['class'=>'form-control datepicker','id'=>'scholarship_start_date']) !!}              
        </div>  
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="form-group">
                {!!Form::label('other_scholarship', 'Other Scholarship')!!}
                {!! Form::text('other_scholarship', $student->AcademicDetail->other_scholarship, ['class'=>'form-control','id'=>'other_scholarship']) !!}              
        </div>  
    </div>
    <div class="col-lg-12 text-right">
        @if(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin'))
        <button type="submit" class="btn btn-sm btn-primary btn-icon-split" id="frmScholarshipSubmit" name="frmScholarshipSubmit"><span class="icon"><i class="fa fa-paper-plane"></i></span> <span class="text">Update Scholarship</span></button>
        @endif
    </div>
</div>
{{ Form::close() }}

<h5 class="alert bg-gray-700 text-white mt-3">Remarks</h5>
<div class="row">
    <div class="col-lg-12 text-right text-white ">
        @if(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin'))
        <a class="btn btn-sm btn-primary btn-icon-split" id="addAchievment"><span class="icon"><i class="fa fa-plus"></i></span> <span class="text">Add Remarks</span></a>
        @endif
    </div>
</div>
@if($achievments)
    @foreach($achievments as $ach)
            <div class="row ml-3" style="font-size: 1rem">
            @if($ach->type==1)
                <div class="col-md-12 mt-2 text-success border-success" style="border:1px">
                    <i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;{{ $ach->comment}}
                </div>
            @else
                <div class="col-md-12 mt-2 text-danger">
                    <i class="fa fa-minus"></i>&nbsp;&nbsp;&nbsp;{{ $ach->comment}}
                </div>
            @endif
            </div>
    @endforeach
@endif