@extends('layouts.admin')

@section('title')
System Settings
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">system Settings</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2 border-left-danger">
    <div class="card-body">
        {{ Form::open(['url' => '#', 'id'=>'editRecordFrom']) }}
            <h4 class="text-secondary mb-0 mt-3"><i data-feather="settings" class="mr-0 pb-1"></i> Settings</h4>
            <hr class="mb-3 mt-0"/>
            <div class="form-group row">
                <label for="year" class="col-sm-3 col-form-label">Current Processing Year</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control yearpicker" id="year" value="{{settings('year')}}" name="Settings[year]">
                </div>
            </div>
            
            <div class="form-group row">
                <label for="" class="col-sm-3">Year Registration Renewal</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_year_reg]','0',settings('enable_year_reg')==0?true:false, ['class'=>'custom-control-input','id'=>'enable_year_reg_0','required'=>'required']) !!}
                        {!!Form::label('enable_year_reg_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_year_reg]','1',settings('enable_year_reg')==1?true:false, ['class'=>'custom-control-input','id'=>'enable_year_reg_1','required'=>'required']) !!}
                        {!!Form::label('enable_year_reg_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="reg_year" class="col-sm-3 col-form-label">Registration Renewal Year</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control yearpicker" id="reg_year" value="{{settings('reg_year')}}" name="reg_year">
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-3">Semester Registration</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_semester_reg]','0',settings('enable_semester_reg')==0?true:false, ['class'=>'custom-control-input','id'=>'enable_semester_reg_0','required'=>'required']) !!}
                        {!!Form::label('enable_semester_reg_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_semester_reg]','1',settings('enable_semester_reg')==1?true:false, ['class'=>'custom-control-input','id'=>'enable_semester_reg_1','required'=>'required']) !!}
                        {!!Form::label('enable_semester_reg_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="reg_year" class="col-sm-3 col-form-label">Semester Registration Starting Semester</label>
                <div class="col-sm-2">
                    {!! Form::select('Settings[sem_reg_min_semester]',[1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8],settings('sem_reg_min_semester'), ['class'=>'form-control','id'=>'sem_reg_min_semester','required'=>'required']) !!}              
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-3">Exam Registration</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_exam_reg]','0',settings('enable_exam_reg')==0?true:false, ['class'=>'custom-control-input','id'=>'enable_exam_reg_0','required'=>'required']) !!}
                        {!!Form::label('enable_exam_reg_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[enable_exam_reg]','1',settings('enable_exam_reg')==1?true:false, ['class'=>'custom-control-input','id'=>'enable_exam_reg_1','required'=>'required']) !!}
                        {!!Form::label('enable_exam_reg_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-3">Exam Admission Download</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[exam_app_download]','0',settings('exam_app_download')==0?true:false, ['class'=>'custom-control-input','id'=>'exam_app_download_0','required'=>'required']) !!}
                        {!!Form::label('exam_app_download_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[exam_app_download]','1',settings('exam_app_download')==1?true:false, ['class'=>'custom-control-input','id'=>'exam_app_download_1','required'=>'required']) !!}
                        {!!Form::label('exam_app_download_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="" class="col-sm-3">Specialization Selection</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[sp_selection_enable]','0',settings('sp_selection_enable')==0?true:false, ['class'=>'custom-control-input','id'=>'sp_selection_enable_0','required'=>'required']) !!}
                        {!!Form::label('sp_selection_enable_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[sp_selection_enable]','1',settings('sp_selection_enable')==1?true:false, ['class'=>'custom-control-input','id'=>'sp_selection_enable_1','required'=>'required']) !!}
                        {!!Form::label('sp_selection_enable_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="reg_year" class="col-sm-3 col-form-label">Specialization Selection Semester</label>
                <div class="col-sm-2">
                    {!! Form::select('Settings[sp_select_semster]',[1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8],settings('sp_select_semster'), ['class'=>'form-control','id'=>'sp_select_semster','required'=>'required']) !!}              
                </div>
            </div>
            <div class="form-group row">
                <label for="batch_size" class="col-sm-3">Batch Size</label>
                <div class="col-sm-2">
                    <input type="number" class="form-control" id="batch_size" value="{{settings('batch_size')}}" name="Settings[batch_size]">
                </div>
            </div>

            <div class="form-group row">
                <label for="" class="col-sm-3">Show Results to Students</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[std_show_results]','0',settings('std_show_results')==0?true:false, ['class'=>'custom-control-input','id'=>'std_show_results_0','required'=>'required']) !!}
                        {!!Form::label('std_show_results_0', 'Disable',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('Settings[std_show_results]','1',settings('std_show_results')==1?true:false, ['class'=>'custom-control-input','id'=>'std_show_results_1','required'=>'required']) !!}
                        {!!Form::label('std_show_results_1', 'Enable',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-3 offset-md-3">
                    <button type="submit" class="btn btn-primary btn-grey-overlay" id="BtnFormSubmit">Update</button>
                </div>
            </div>
        </form>
    </div>
</div> 
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>

$(document).ready(function() {
    $('.yearpicker').datepicker({
        format:'yyyy',
        viewMode: "years", 
        minViewMode: "years"
    });
    

    // $('#BtnFilterSubmit').on('click',function(e){
    //     e.preventDefault();
    // });

    // $('body').on('click','.edit-record',function(e){
    //     e.preventDefault();
    //     var id = $(this).data('id');
    //     var data = table.row($(this).parents('tr')).data();

    //     $('#id').val(id);
    //     $('#name').val(data['Name']);
    //     $('#code').val(data['Code']);
    //     $('#semester').val(data['Semester']);
    //     $('#credits').val(data['Credits']);
    //     $('#type').val(data['Type']);
    //     $('#specialization_category').val(data['Specialization']);

        

    //     if(data['Type']=='C'){
    //         $('#type_0').prop("checked", true);
    //     }else{
    //         $('#type_1').prop("checked", true);
    //     }
       
    //     if(data['Status']==1){
    //         $('#status_1').prop("checked", true);
    //     }else{
    //         $('#status_0').prop("checked", true);
    //     }

    //     $('#editRecord').modal('show');
    // });

    var validater = $('#editRecordFrom').validate();

    $('#BtnFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#editRecordFrom').valid()){
            $.post("{{url('/admin/settings/update-system-settings')}}",$('#editRecordFrom').serialize(),function(data){
                if(data==1)location.reload();
            });
        }

    });

});

</script>
@endsection

@section('modal')

@endsection




