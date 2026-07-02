@extends('layouts.admin')

@section('title')
System Logs
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">System Logs</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection


@section('content')
<div class="card mb-2 border-left-danger">
    <div class="card-body">
        <form>
            <h4 class="text-secondary mb-0 mt-3"><i data-feather="search" class="mr-0 pb-1"></i> Search</h4>
            <hr class="mb-3 mt-0"/>
            <div class="row">
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                            {!!Form::label('filter_module', 'Module')!!}
                            {!! Form::select('filter_module',[''=>'','Login'=>'Login','Exam'=>'Exam Application','Results'=>'Results'],'', ['class'=>'form-control','id'=>'filter_module']) !!}              
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
        <div class="row mt-3">
            <div class="col-md-12">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [100,200],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/system-logs')}}",
                "data" : function ( d ) {
                    d.module = $('#filter_module').val();
                    d.type = 'json';
                },
		},
    "order": [[ 1, "desc" ]],
		"columns": [

        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "Date", "name": "Date", "title": "Date","width": "8rem"},
        { "data": "User", "name":"User", "title": "User" },
        { "data": "IP", "name":"IP", "title": "IP","width": "5rem", 'class' : 'text-center'},
        { "data": "Module", "name":"Module", "title": "Module","width": "5rem", 'class' : 'text-center'},
        { "data": "Description", "name":"Description", "title": "Description","orderable": false, }
        ]
	});

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
    });
});
</script>
@endsection
