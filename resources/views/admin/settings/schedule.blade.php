@extends('layouts.admin')

@section('title')
Manage Schedules
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">Schedules</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Search">Search</label>
                        <input type="input" class="form-control" id="Search" name="Search">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                        <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit">
                            <span class="icon"><i class="fa fa-search"></i></span>
                            <span class="text">Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div> 

<div class="card border-left-danger">
    <div class="card-body">
        <table id="grid" class="table table-striped compact table-bordered dataTable">
        </table>
    </div>
</div>
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {

    $('.datepicker').datepicker({
        format:'yyyy-mm-dd',
    });
    
    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [10, 25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/schedules')}}",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.type = 'json';
                },
		},
    "order": [[ 2, "asc" ]],
		"columns": [
        {
            "data": "Action", 'class' : 'text-center',
            render: function (data, type, full, meta) {
                var html ='<a href="#" class="btn btn-primary btn-xs edit-record" data-id="'+full.ID +'"><i class="fa fa-pencil-alt "></i></a> &nbsp;';
                        // html +='<a href="#" class="btn btn-danger btn-sm delete-record" data-id="'+full.ID +'"><i class="fa fa-trash-alt"></i></a>';
                return html;
            },
            "orderable": false, 
            "width": "5rem",
		},
        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "Name", "name":"Name", "title": "Name" , "width":"20rem"},
        { "data": "StartDate", "title": "StartDate", "name":"Start Date","width": "15rem",  },
		{ "data": "OverdueDate",  "name": "OverdueDate" , "title": "Overdue Date","width": "15rem", }	,
        { "data": "EndDate",  "name": "EndDate" , "title": "End Date","width": "15rem", },
        {
            "data": "IsEnabled", 'class' : 'text-center',
            render: function (data, type, full, meta) {
                return (full.IsEnabled==1)?'Yes':'No';
            },
            "orderable": false, 
            "width": "5rem",
            "title": "Enabled" 
		},	
				
		]
    });
    
    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
    });

    $('body').on('click','.edit-record',function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var data = table.row($(this).parents('tr')).data();

        $('#id').val(id);
        $('#name').val(data['Name']);
        $('#start_date').datepicker('setDate', data['StartDate']);
        $('#overdue_date').datepicker('setDate', data['OverdueDate']);
        $('#end_date').datepicker('setDate', data['EndDate']);
        if(data['IsEnabled']==1){
            $('#is_enabled_1').prop("checked", true);
        }else{
            $('#is_enabled_0').prop("checked", true);
        }
        $('#editRecord').modal('show');
    });

    var validater = $('#editRecordFrom').validate();

    $('#BtnFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#editRecordFrom').valid()){
            $.post("{{url('/admin/settings/update-schedule')}}",$('#editRecordFrom').serialize(),function(data){
                $('#editRecord').modal('hide');
                table.ajax.reload();
            });
        }

    });

});

</script>
@endsection


@section('modal')
<div class="modal fade" id="editRecord" role="dialog" aria-labelledby="editRecordTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRecordTitle">Edit Schedules</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'editRecordFrom']) }}
                <input type="hidden" value="" name="id" id="id"/>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                                {!!Form::label('name', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'name','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('', 'Enabled',['class'=>'d-block'])!!}
                                <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('is_enabled','0',true, ['class'=>'custom-control-input','id'=>'is_enabled_0','required'=>'required']) !!}
                                {!!Form::label('is_enabled_0', 'Disable',['class'=>'custom-control-label'])!!}
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    {!! Form::radio('is_enabled','1',true, ['class'=>'custom-control-input','id'=>'is_enabled_1','required'=>'required']) !!}
                                    {!!Form::label('is_enabled_1', 'Enable',['class'=>'custom-control-label'])!!}
                                </div>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('start_date', 'StartDate')!!}
                                {!! Form::text('start_date','', ['class'=>'form-control datepicker','id'=>'start_date','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('overdue_date', 'OverdueDate')!!}
                                {!! Form::text('overdue_date','', ['class'=>'form-control datepicker','id'=>'overdue_date','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('end_date', 'EndDate')!!}
                                {!! Form::text('end_date','', ['class'=>'form-control datepicker','id'=>'end_date','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary btn-grey-overlay btn-grey-overlay mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>
@endsection



