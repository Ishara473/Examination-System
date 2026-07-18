@extends('layouts.admin')

@section('title')
Students
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/student">Student management</a></li>
@endsection

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('plugins/datatables/datatables-boostrap4.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="card mb-2">
  <div class="card-body">
    <form>
      <a href="#" class="float-right " data-toggle="collapse" data-target="#filterSection" aria-expanded="true" >
        <i data-feather="chevron-down"></i>
    </a>
      <h4 class="text-secondary mb-3 mt-3"><i data-feather="user" class="mr-0 pb-1"></i> Student Search</h4>
        {{-- <hr class="mb-3 mt-0"/> --}}
      <div class="row collapse show" id="filterSection">
        <div class="col-md-3">
          <div class="form-group">
              <label for="Batch" style="width:100%">Batch</label></label>
              <select class="form-control d-inline" id="Batch" name="Batch">
                <option value=""></option>
                @foreach($batches as $key=>$batch)
                  <option value="{{$key}}" @if($key==$curBatch) selected @endif>{{$batch}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
              <div class="form-group">
                  <label for="Search">Registration No / NIC</label>
                  <input type="input" class="form-control" id="Search" name="Search">
              </div>
          </div>
          <div class="col-md-3 col-lg-3">
              <div class="form-group">
                  <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                  <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Search</span></button>
              </div>
          </div>
          <div class="col-md-3 col-lg-3 text-right">
            <div class="form-group">
                <label for="BtnExport" class="d-block">&nbsp;</label>
                <a href="/admin/student/list?type=excel&batch={{$curBatch}}" class="btn btn-info btn-grey-overlay btn-icon-split" id="exportExcel" ><span class="icon"><i class="fa fa-file-excel"></i></span> <span class="text">Export to Excel</span></a>
            </div>
          </div>
        </div>
    </form>
  </div>
</div>
<div class="card mb-2">
  <div class="card-body mt-1">
    <table id="grid" class="table table-striped compact table-bordered dataTable">
    </table>
  </div>
</div> 
@endsection


@section('custom-js')
{{-- <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {
    
    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
        $('#exportExcel').attr('href','/admin/student/list?type=excel&batch='+$('#Batch').val()+'&search='+$('#Search').val());
    });






    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "/admin/student/list",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.batch = $('#Batch').val();
                },
		},
    "order": [[ 2, "asc" ]],
		"columns": [
        {
					"data": "Action",'class' : 'text-center',
					render: function (data, type, full, meta) {
						var html ='<a href="/admin/student/view/' + full.ID + '" class="btn btn-primary btn-xs view-app" data-id="'+full.ID +'"><i class="fa fa-search"></i></a> &nbsp;';
            // html +='<a href="/admin/registration/view-application-payment/' + full.ID + '" class="btn btn-success btn-sm view-app-payment" data-id="'+full.ID +'"><i class="fa fa-dollar-sign pl-1 pr-1"></i></a>';
            return html;
					},
          "orderable": false, 
          "width": "5rem",
				},
				{ "data": "ID",	"visible":false, "name":"ID" },
				{ "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "150px",},
				{ "data": "Name", "name":"Name", "title": "Name with Initials" },
        { "data": "Batch", "title": "Batch", "name":"Batch"  },
				{ "data": "IDNo",  "name": "IDNo" , "title": "ID No" }			
        // { "data": "Gender", "title": "Gender", "name":"Gender"  },
        // { "data": "Email", "title": "Email", "name":"Email"  },
				
		]
	});

});

</script>
@endsection




