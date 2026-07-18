@extends('layouts.admin')

@section('title')
Manage Course Subjects
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">Course Subjects</li>
@endsection

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('plugins/datatables/datatables-boostrap4.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="card mb-2 border-left-danger">
    <div class="card-body">
        <form>
            <h4 class="text-secondary mb-0 mt-3"><i data-feather="search" class="mr-0 pb-1"></i> Search</h4>
                <hr class="mb-3 mt-0"/>
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <label for="Search">Name</label>
                        <input type="input" class="form-control" id="Search" name="Search">
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                            {!!Form::label('filter_semester', 'Semester')!!}
                            {!! Form::select('filter_semester',[''=>'',1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8],'', ['class'=>'form-control','id'=>'filter_semester']) !!}              
                    </div>  
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                            {!!Form::label('filter_type', 'Type')!!}
                            {!! Form::select('filter_type',[''=>'','C'=>'Compulsory','S'=>'Specialized'],'', ['class'=>'form-control','id'=>'filter_type']) !!}              
                    </div>  
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                            {!!Form::label('filter_regulation', 'Regulation')!!}
                            {!! Form::select('filter_regulation',$regulations,$currnt_regulation, ['class'=>'form-control','id'=>'filter_regulation']) !!}              
                    </div>  
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-group">
                        <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                        <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Search</span></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div> 

<div class="card border-left-danger">
    <div class="card-body">
        <div class="row mt-4"><div class="col-md-12 text-right"><a href="#" class="btn btn-primary btn-grey-overlay btn-icon-split btn-xs" id="addSubject"><span class="icon"><i class="fa fa-plus"></i></span> <span class="text mt-1">Add New Subject</span></a> </div></div>
        <div class="row">
            <div class="col-md-12">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                </table>
            </div>
        </div>
    </div>
</div> 
@endsection


@section('custom-js')
{{-- <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
// var specialization = { @foreach($specializations as $specialization) "{{$specialization->id}}":"{{$specialization->name}}", @endforeach 0:''};
var SubjectType = {'C':'Compulsory','E':'Elective','S':'Specialized'};
$(document).ready(function() {
    $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [10, 25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/courses')}}",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.search_type = $('#filter_type').val();
                    d.search_regulation = $('#filter_regulation').val();
                    d.search_semester = $('#filter_semester').val();
                    d.type = 'json';
                },
		},
    "order": [[ 2, "asc" ]],
		"columns": [
        {
            "data": "Action",'class' : 'text-center',
            render: function (data, type, full, meta) {
                var html ='<a href="#" class="btn btn-primary btn-sm edit-record" data-id="'+full.ID +'"><i class="fa fa-pencil-alt "></i></a>&nbsp;';
                html +='<a href="#" class="btn btn-success btn-sm assign-lecturer" data-id="'+full.ID +'"><i class="fa fa-user "></i></a>&nbsp;';
                if(full.Type=='S') html +='<a href="#" class="btn btn-info btn-sm edit-specilization" data-id="'+full.ID +'"><i class="fa fa-layer-group "></i></a>&nbsp;';
                
                return html;
            },
          "orderable": false, 
          "width": "8rem",
		},
        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "Code", "name": "Code", "title": "Code","width": "8rem"},
        { "data": "Name", "name":"Name", "title": "Name" },
        { "data": "Year", "name":"Year", "title": "Year","width": "5rem", 'class' : 'text-center'},
        { "data": "Semester", "name":"Semester", "title": "Semester","width": "5rem", 'class' : 'text-center'},
        { "data": "Credits", "name":"Credits", "title": "Credits","width": "5rem", 'class' : 'text-center',"orderable": false, },
        {
            "data": "Type", 'class' : 'text-center',
            render: function (data, type, full, meta) {
                return SubjectType[full.Type];
            },
            "width": "5rem",
            "title": "Type" 
		},
        {
            "data": "Status", 'class' : 'text-center',
            render: function (data, type, full, meta) {
                var status = {"-1":'Repeat Only',"1":'Enabled', "0":'Disabled'};
                return status[full.Status];
            },
            "orderable": false, 
            "width": "5rem",
            "title": "Enabled" 
		},

        { "data": "Amount",  "name":"Amount", "visible":false, },
        { "data": "Surcharge",  "name": "Surcharge" , "visible":false, },
        { "data": "Order",  "name": "Order" , "visible":false, }			

				
		]
	});

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    $('body').on('click','#addSubject',function(e){
        e.preventDefault();
        $('#addRecordFrom')[0].reset();
        $('#addRecordModel').modal('show');
    });

    var validater_add = $('#addRecordFrom').validate();

    $('#BtnAddFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#addRecordFrom').valid()){
            $.post("{{url('/admin/settings/add-course')}}",$('#addRecordFrom').serialize(),function(data){
                $('#addRecordModel').modal('hide');
                table.ajax.reload();
            });
        }
    });


    $('body').on('click','.edit-record',function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var data = table.row($(this).parents('tr')).data();
        console.log(data);
        $('#id').val(id);
        $('#name').val(data['Name']);
        $('#code').val(data['Code']);
        $('#semester').val(data['Semester']);
        $('#credits').val(data['Credits']);
        $('#type').val(data['Type']);   
        $('#regulation').val(data['Regulation']);     
        $('#display_order').val(data['Order']);     



        if(data['Type']=='C'){
            $('#type_0').prop("checked", true);
        }else{
            $('#type_1').prop("checked", true);
        }
       
        if(data['Status']==1){
            $('#status_1').prop("checked", true);
        }else if(data['Status']==-1){
            $('#status_-1').prop("checked", true);
        }else{
            $('#status_0').prop("checked", true);
        }

        $('#editRecord').modal('show');
    });

    var validater = $('#editRecordFrom').validate();

    $('#BtnFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#editRecordFrom').valid()){
            $.post("{{url('/admin/settings/update-course')}}",$('#editRecordFrom').serialize(),function(data){
                $('#editRecord').modal('hide');
                table.ajax.reload();
            });
        }
    });

    $('body').on('click','.edit-specilization',function(e){
        e.preventDefault();
        $('.specializationSelecction').val('');
        var subjectId = $(this).data('id');
        $('#spcid').val(subjectId);
        $.get('{{url('/admin/settings/courses-specialization')}}',{subjectId:subjectId},function(data){
            if($.trim(data)){
                $.each(data, function(index,value){
                    $('#sp_'+index).val(value);
                });
            }
            $('#editSpecilization').modal('show');
        });
    });

    $('body').on('click','#BtnSpecilizationFormSubmit',function(e){
        e.preventDefault();
        $.post('{{url('/admin/settings/update-course-specialization')}}',$('#editSpecilizationForm').serialize(),function(response){
            $('#editSpecilization').modal('hide');
            $('.specializationSelecction').val('');
        });
    });

    $('body').on('click','.assign-lecturer',function(e){
        e.preventDefault();
        var subjectId = $(this).data('id');
        $('#ALFSid').val(subjectId);
        $.get('{{url('/admin/settings/courses-lectuerer')}}',{id:subjectId},function(data){
            if($.trim(data)){
                var html = '';
                $.each(data, function(index,row){
                   html += '<div class="col-md-4"><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" name="lecturer['+row.Id+']" value="'+row.Id+'" id="lec_assign_'+row.Id+'" class="custom-control-input" ';
                   if(row.IsAssigned==1) html +=' checked="checked" ' 
                   html +='/><label for="lec_assign_'+row.Id+'" class="custom-control-label">'+row.Name+'</label></div></div>';
                });
                $('#lecturerList').html(html);
            }
            $('#assignLecturers').modal('show');
        });
    });

    $('body').on('click','#BtnLecAssignFormSubmit',function(e){
        e.preventDefault();
        $.post('{{url('/admin/settings/assign-courses-lectuerer')}}',$('#assignLecturersForm').serialize(),function(response){
            $('#assignLecturers').modal('hide');
        });
    });

    
});

</script>
@endsection

@section('modal')
<div class="modal fade" id="addRecordModel" role="dialog" aria-labelledby="addRecordTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecordTitle">Add Course Subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'addRecordFrom']) }}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('addCode', 'Code')!!}
                                {!! Form::text('code','', ['class'=>'form-control','id'=>'addCode','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                                {!!Form::label('addName', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'addName','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <div class="row">  
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('addRegulation', 'Regulation',['class'=>'d-block'])!!}
                            {!! Form::select('regulation',$regulations,'', ['class'=>'form-control','id'=>'addRegulation','required'=>'required']) !!}              
                        </div> 
                    </div>                                                
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('addSemester', 'Semester')!!}
                                {!! Form::select('semester',[""=>"",1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8],'', ['class'=>'form-control','id'=>'addSemester','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('addCredits', 'Credits')!!}
                                {!! Form::text('credits','', ['class'=>'form-control','id'=>'addCredits','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('addType', 'Type',['class'=>'d-block'])!!}
                            {!! Form::select('type',[''=>'','C'=>'Compulsory','E'=>'Elective','S'=>'Specialized'],'', ['class'=>'form-control','id'=>'addType','required'=>'required']) !!}
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('addDisplayOrder', 'Order')!!}
                                {!! Form::text('display_order','', ['class'=>'form-control','id'=>'addDisplayOrder','required'=>'required']) !!}              
                        </div>  
                    </div>                     
                    <div class="col-md-6">
                        <div class="form-group">
                                <label class="d-block">Enable</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','0',true, ['class'=>'custom-control-input','id'=>'addStatus_0','required'=>'required']) !!}
                                    {!!Form::label('addStatus_0', 'Disable',['class'=>'custom-control-label'])!!}
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','1',true, ['class'=>'custom-control-input','id'=>'addStatus_1','required'=>'required']) !!}
                                    {!!Form::label('addStatus_1', 'Enable',['class'=>'custom-control-label'])!!}
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','-1',true, ['class'=>'custom-control-input','id'=>'addStatus_-1','required'=>'required']) !!}
                                    {!!Form::label('addStatus_-1', 'Repeat Only',['class'=>'custom-control-label'])!!}
                                </div>
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-grey-overlay mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnAddFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editRecord" role="dialog" aria-labelledby="editRecordTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRecordTitle">Edit Course Subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'editRecordFrom']) }}
                <input type="hidden" value="" name="id" id="id"/>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('code', 'Code')!!}
                                {!! Form::text('code','', ['class'=>'form-control','id'=>'code','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                                {!!Form::label('name', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'name','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <div class="row"> 
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('regulation', 'Regulation',['class'=>'d-block'])!!}
                            {!! Form::select('regulation',$regulations,'', ['class'=>'form-control','id'=>'regulation','required'=>'required']) !!}              
                        </div> 
                    </div>                                                   
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('semester', 'Semester')!!}
                                {!! Form::select('semester',[1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8],'', ['class'=>'form-control','id'=>'semester','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('credits', 'Credits')!!}
                                {!! Form::text('credits','', ['class'=>'form-control','id'=>'credits','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::label('type', 'Type',['class'=>'d-block'])!!}
                            {!! Form::select('type',[''=>'','C'=>'Compulsory','E'=>'Elective','S'=>'Specialized'],'', ['class'=>'form-control','id'=>'type','required'=>'required']) !!}                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('display_order', 'Order')!!}
                                {!! Form::text('display_order','', ['class'=>'form-control','id'=>'display_order','required'=>'required']) !!}              
                        </div>  
                    </div>           
                    <div class="col-md-6">
                        <div class="form-group">
                                <label class="d-block">Enable</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','0',true, ['class'=>'custom-control-input','id'=>'status_0','required'=>'required']) !!}
                                    {!!Form::label('status_0', 'Disable',['class'=>'custom-control-label'])!!}
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','1',true, ['class'=>'custom-control-input','id'=>'status_1','required'=>'required']) !!}
                                    {!!Form::label('status_1', 'Enable',['class'=>'custom-control-label'])!!}
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('status','-1',true, ['class'=>'custom-control-input','id'=>'status_-1','required'=>'required']) !!}
                                    {!!Form::label('status_-1', 'Repeat Only',['class'=>'custom-control-label'])!!}
                                </div>
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-grey-overlay mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSpecilization" role="dialog" aria-labelledby="editSpecializationTitle" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSpecilizationTitle">Edit Specialization Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'editSpecilizationForm','method'=>'POST']) }}
                <input type="hidden" value="" name="spcid" id="spcid"/>

                @if(!empty($specializations))
                @foreach($specializations as $specialization) 
                <div class="form-group row">
                    <label for="sp" class="col-sm-8 col-form-label">{{$specialization->name}}</label>
                    <div class="col-sm-4">
                        <select name="sp[{{$specialization->id}}]" id="sp_{{$specialization->id}}" class="form-control specializationSelecction" style="width:100%" >
                            <option value=""></option>
                            <option value="C">Compulsory</option>
                            <option value="E">Elective</option>
                        </select>
                    </div>
                  </div>
                @endforeach
                @endif
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-grey-overlay mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnSpecilizationFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignLecturers" role="dialog" aria-labelledby="assignLecturersTitle" >
    <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignLecturersTitle">Select Lecturers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'assignLecturersForm','method'=>'POST']) }}
                <input type="hidden" value="" name="subjectId" id="ALFSid"/>
                <div class="row" id="lecturerList">
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-grey-overlay mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnLecAssignFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>
@endsection




