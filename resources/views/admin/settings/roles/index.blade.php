@extends('layouts.admin')

@section('title')
Roles
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">Roles</li>
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
        <div class="row mt-3">
            <div class="col-md-12 text-right">
                <a href="{{url('/admin/settings/add-roles')}}" class="btn btn-success btn-grey-overlay btn-icon-split"><span class="icon"><i class="fa fa-plus"></i></span> <span class="text">Add New Role</span></a>
            </div>
        </div>

        <table id="grid" class="table table-striped compact table-bordered dataTable">
        </table>
    </div>
</div>
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {
    
    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [10, 25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/roles')}}",
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
                var html ='<a href="{{url('/admin/settings/update-roles')}}/'+full.ID +'" class="btn btn-primary btn-xs edit-record" data-id="'+full.ID +'"><i class="fa fa-pencil-alt "></i></a> &nbsp;';
                // html +='<a href="#" class="btn btn-danger btn-sm delete-record" data-id="'+full.ID +'"><i class="fa fa-trash-alt"></i></a>';
                return html;
			},
            "orderable": false, 
            "width": "5rem",
		},
        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "Name", "name":"Name", "title": "Name","width": "15rem",  },
        { "data": "Description", "name":"Description", "title": "Description", "orderable": false, },
		]
    });

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
    });

});

</script>
@endsection


@section('modal')

@endsection