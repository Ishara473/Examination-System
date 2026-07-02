@extends('layouts.admin')

@section('title')
Semester Result Transcript
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Transcripts</a></li>
<li class="breadcrumb-item">Statement of Semester Results</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <form>
        <a href="#" class="float-right " data-toggle="collapse" data-target="#filterSection" >
            <i data-feather="chevron-down"></i>
        </a>
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="printer" class="mr-0 pb-1"></i> Print Statement of Semester Results</h4>
        <div class="row" id="filterSection">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="Semester">Semester</label>
                    <select id="Semester" name="Semester" class="form-control">
                        <option value="" selected></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="Year">Year</label>
                <input type="input" class="form-control yearpicker" id="Year" name="Year" value="{{settings('year')}}">
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
                    <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Get Applications</span></button>
                </div>
            </div>
        </div>
        </form>
        <div class="row mt-3" >
            <div class="col-md-10 col-lg-10">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                    <thead>
                        <th style="padding:4px"><div class="form-check"><input class="form-check-input position-static " id="SACheckAll"  type="checkbox" value="1"></div></th>
                        <th></th>
                        <th>RegistrationNo</th>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>IDNo</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2 col-lg-2">
                <div class="row">
                    <div class="col-md-12 col-lg-12 ">
                        <div class="form-group">
                            <label for="StartDate">Exam Start Date</label>
                            <input type="input" class="form-control datepicker" id="StartDate" name="StartDate">
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 ">
                        <div class="form-group">
                            <label for="EndDate">Exam End Date</label>
                            <input type="input" class="form-control datepicker" id="EndDate" name="EndDate">
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12  ">
                        <div class="form-group">
                            <label for="Type">Attempt Type</label>
                            <select id="Type" name="Type" class="form-control">
                                <option value="1">Proper</option>
                                <option value="2">Repeat</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <input type="hidden" name="year" id="ProcessingYear" value="" />
                            <input type="hidden" name="semester" id="ProcessingSemester" value="" />
                            <button class="btn btn-success btn-grey-overlay btn-icon-split" id="BtnDownload" name="BtnDownload"><span class="icon"><i class="fa fa-print"></i></span> <span class="text">Download Statement of Semester Results</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>

<script>
$(document).ready(function() {

    $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

    $('.yearpicker').datepicker({
        format:'yyyy',
        viewMode: "years", 
        minViewMode: "years"
    });

    $('.datepicker').datepicker({
        format:'yyyy-mm-dd',
        viewMode: "years", 
        minViewMode: "days"
    });


    var table = $('#grid').DataTable({
        "dom": 'Bltip',
        // "lengthMenu": [25, 50, 75, 100],
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "paging":false,
        "deferLoading": 0,
        "ajax": {
                "url": "{{url('/admin/transcripts/semester-transcripts')}}",
                "data" : function ( d ) {
                    d.type='json';
                    d.year = $('#Year').val();
                    d.semester = $('#Semester').val();
                    d.search = $('#Search').val();
                },
        },
        "order": [[ 2, "asc" ]],
        "columns": [
            {
                'targets': 0,
                "data": "Action",'class' : 'text-center pl-0',
                render: function (data, type, full, meta) {
                    var html ='<div class="form-check"><input class="form-check-input position-static SACheckBox" id="SA_' + full.ID + '" name="SA[' + full.ID + ']" type="checkbox" value="' + full.ID + '" /></div>';
                    return html;
                },
                "orderable": false, 
                "width": "2rem",
            },
        { "data": "ID",	"visible":false, "name":"ID" ,"orderable": false },
        { "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "150px",},
        { "data": "Name", "name":"Name", "title": "Name with Initials" },
        { "data": "Batch", "title": "Batch", "name":"Batch"  },
        { "data": "IDNo",  "name": "IDNo" , "title": "ID No" }	
        ]
    });

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        if($('#Semester').val() != '' && $('#Year').val() != '' ){
            $('#SACheckAll').prop('checked',false);
            $('#ProcessingYear').val($('#Year').val());
            $('#ProcessingSemester').val($('#Semester').val());
            table.ajax.reload();
        }
    });

    $('#SACheckAll').prop('checked',false);
    $('#SACheckAll').on('change',function(e){
        e.preventDefault();
        if($(this).prop("checked") == true){
            $('.SACheckBox').prop('checked',true);
        }else{
            $('.SACheckBox').prop('checked',false);
        }
    });

    $('#BtnDownload').on('click',function(e){
        e.preventDefault();
        var type = parseInt($('#Type').val());
        var semester = parseInt($('#ProcessingSemester').val());
        var year = parseInt($('#ProcessingYear').val());
        var startdate = $('#StartDate').val();
        var enddate = $('#EndDate').val();
        var students = $(".SACheckBox:checked").map(function(){return $(this).val();}).get();
        if(students.length > 0) window.location = "{{url('/admin/transcripts/semester-transcripts-download')}}?type="+type+'&semester='+semester+"&students="+students+'&year='+year+"&startdate="+startdate+'&enddate='+enddate;
    });



});

</script>
@endsection




