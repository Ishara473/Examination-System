@extends('layouts.admin')

@section('title')
Yearly Registration
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Registration Management</a></li>
<li class="breadcrumb-item">Year Registration</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">

@endsection

@section('content')
<div class="card mb-2">
  <div class="card-body">
    <form>
        <a href="#" class="float-right " data-toggle="collapse" data-target="#filterSection" aria-expanded="true"><i data-feather="chevron-down"></i></a>
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="user" class="mr-0 pb-1"></i> Yearly Student Registration</h4>
        <div class="row collapse show" id="filterSection">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="ACCYear">Academic Year</label>
                <input type="input" class="form-control yearpicker" id="ACCYear" name="ACCYear" value="{{settings('year')-1}}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="StudyYear">Study Year</label>
                    <select id="StudyYear" name="StudyYear" class="form-control">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
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
<div class="card mb-2">
    <div class="card-body mt-1">
    <table id="grid" class="table table-striped compact table-bordered dataTable"></table>
  </div>
</div> 
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {
    
    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
        // $('#exportExcel').attr('href','/admin/registration/list?type=excel&startDate='+$('#StartDate').val()+'&endDate='+$('#EndDate').val()+'&search='+$('#Search').val());
    });


    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "/admin/registration/view-year-registration",
                "data" : function ( d ) {
                    d.studyyear = $('#StudyYear').val();
                    d.accyear = $('#ACCYear').val();
                    d.type = 'json';
                },
		},
    "order": [[ 2, "asc" ]],
		"columns": [
				{ "data": "ID",	"visible":false, "name":"ID" },
				{ "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "150px",},
				{ "data": "Name", "name":"Name", "title": "Name with Initials" },
                { "data": "Batch", "title": "Batch", "name":"Batch"  },
				{ "data": "IDNo",  "name": "IDNo" , "title": "ID No" },
                { "data": "AcademicYear", "title": "Academic Year", "name":"AcademicYear"  },
                { "data": "StudyYear", "title": "Study Year", "name":"StudyYear"  },


				
		]
	});

    $('.yearpicker').datepicker({
        format:'yyyy',
        viewMode: "years", 
        minViewMode: "years"
    });
});

</script>
@endsection




