@extends('layouts.admin')

@section('title')
Regulations
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">Regulations</li>
@endsection

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('plugins/datatables/datatables-boostrap4.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="card border-left-danger">
    <div class="card-body">
        <div class="row"><div class="col-md-12 text-right mt-4"><a href="#" class="btn btn-primary btn-grey-overlay btn-icon-split btn-xs" id="addRegulation"><span class="icon"><i class="fa fa-plus"></i></span> <span class="text mt-1">Add New Regulation</span></a> </div></div>
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
var Status = {'0':'No','1':'Yes'};
$(document).ready(function() {
    
    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [10, 25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/regulation')}}",
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
            { "data": "Name", "name":"Name", "title": "Name" },
            { "data": "Version", "title": "Version", "name":"Version" },
            { "data": "BylawVer", "title": "By law", "name":"BylawVer" },
            { "data": "IsCurrent",  "name": "IsCurrent" , "title": "Is Current" ,
                render: function (data, type, full, meta) {
                    return Status[full.IsCurrent];
                },
            }
		]
    });


    $('body').on('click','#addRegulation',function(e){
        e.preventDefault();
        $('#addRecordFrom')[0].reset();
        $('#addRecordModel').modal('show');
    });

    var validater_add = $('#addRecordFrom').validate();

    $('#BtnAddFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#addRecordFrom').valid()){
            $.post("{{url('/admin/settings/add-regulation')}}",$('#addRecordFrom').serialize(),function(data){
                $('#addRecordModel').modal('hide');
                table.ajax.reload();
            });
        }
    });

    
    $('body').on('click','.edit-record',function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var data = table.row($(this).parents('tr')).data();
        $('#id').val(id);
        $('#name').val(data['Name']);
        $('#version').val(data['Version']);
        $('#bylaw').val(data['BylawVer']);
        if(data['IsCurrent']==1) $('#iscurrent_1').prop("checked", true);
        else $('#iscurrent_0').prop("checked", true);
        $('#editRecord').modal('show');
    });

    var validater = $('#editRecordFrom').validate({
        errorClass:'border-danger text-danger'
    });

    $('#BtnFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#editRecordFrom').valid()){
            $.post("{{url('/admin/settings/update-regulation')}}",$('#editRecordFrom').serialize(),function(data){
                $('#editRecord').modal('hide');
                table.ajax.reload();
            });
        }

    });



});

</script>
@endsection


@section('modal')
<div class="modal fade" id="addRecordModel" role="dialog" aria-labelledby="addRecordTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecordTitle">Add Regulation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'addRecordFrom']) }}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                                {!!Form::label('addName', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'addName','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            {!!Form::label('addVersion', 'Version')!!}
                            {!!Form::text('version','', ['class' => 'form-control','id'=>'addVersion'])!!}   
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            {!!Form::label('addBylawVer', 'By Law Version')!!}
                            {!!Form::text('bylaw','', ['class' => 'form-control','id'=>'addBylawVer'])!!}   
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('addIsCurrent', 'Is Current',['class'=>'d-block'])!!}
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('iscurrent','0', true, ['class'=>'custom-control-input','id'=>'addIsCurrent_0']) !!}
                                {!!Form::label('addIsCurrent_0', 'No',['class'=>'custom-control-label'])!!}
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('iscurrent','1', false, ['class'=>'custom-control-input','id'=>'addIsCurrent_1']) !!}
                                {!!Form::label('addIsCurrent_1', 'Yes',['class'=>'custom-control-label'])!!}
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
                <h5 class="modal-title" id="editRecordTitle">Edit Regulation</h5>
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
                                {!!Form::label('name', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'name','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            {!!Form::label('version', 'Version')!!}
                            {!!Form::text('version','', ['class' => 'form-control','id'=>'version'])!!}   
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <div class="form-group">
                            {!!Form::label('bylaw', 'By Law Version')!!}
                            {!!Form::text('bylaw','', ['class' => 'form-control','id'=>'bylaw'])!!}   
                        </div>  
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <div class="form-group">
                            {!!Form::label('iscurrent', 'Is Current',['class'=>'d-block'])!!}
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('iscurrent','0', true, ['class'=>'custom-control-input','id'=>'iscurrent_0']) !!}
                                {!!Form::label('iscurrent_0', 'No',['class'=>'custom-control-label'])!!}
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                {!! Form::radio('iscurrent','1', false, ['class'=>'custom-control-input','id'=>'iscurrent_1']) !!}
                                {!!Form::label('iscurrent_1', 'Yes',['class'=>'custom-control-label'])!!}
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
@endsection