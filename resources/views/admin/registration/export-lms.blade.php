@extends('layouts.admin')

@section('title')
Export Registrations to VLE
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/registration/view-year-registration">Registration Management</a></li>
<li class="breadcrumb-item">VLE Export</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <form>
            <a href="#" class="float-right " data-toggle="collapse" data-target="#filterSection" >
                <i data-feather="chevron-down"></i>
            </a>
            <h4 class="text-secondary mb-3 mt-3"><i data-feather="edit-2" class="mr-0 pb-1"></i> Get Students</h4>
            <div class="row" id="filterSection">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="Regulation" style="width:100%">Regulation</label></label>
                        <select class="form-control d-inline" id="Regulation" name="Regulation">
                            <option value="" selected></option>
                            @foreach($regulations as $key=>$regulation)
                                <option value="{{$key}}">{{$regulation}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="Year">Year</label>
                        <select id="Year" name="Year" class="form-control">
                            <option value="" selected></option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                        <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Get Applications</span></button>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-3 col-lg-3 text-right">
                    <div class="form-group">
                        <label for="BtnExport" class="d-block">&nbsp;</label>
                        <a href="#" class="btn btn-info btn-grey-overlay btn-icon-split" id="exportExcel" ><span class="icon"><i class="fa fa-file-excel"></i></span> <span class="text">Download Excel</span></a>
                    </div>
                  </div>
            </div>
        </form>
    </div>
</div>
<div class="card mb-2">
  <div class="card-body mt-1">
        <div class="row mt-1 mb-3">
            <div class="col-md-12">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                    <thead>
                        <th style="padding:4px"><div class="form-check"><input class="form-check-input position-static " id="SACheckAll"  type="checkbox" value="1"></div></th>
                        <th></th>
                        <th>RegistrationNo</th>
                        <th>Name</th>
                        <th>IDNo</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 
@endsection


@section('custom-js')
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {

    $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


    $('#Year').val("");
    $('#Regulation').val("");

    var table = $('#grid').DataTable({
        "dom": 'Bltip',
        // "lengthMenu": [25, 50, 75, 100],
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "paging":false,
        "deferLoading": 0,
        "ajax": {
                "url": "/admin/registration/export-to-lms",
                "data" : function ( d ) {
                    d.type='json';
                    d.year = $('#Year').val();
                    d.regulation = $('#Regulation').val();
                },
        },
        "order": [[ 2, "asc" ]],
        "columns": [
        {
            'targets': 0,
            "data": "Action",'class' : 'text-center pl-0',
            render: function (data, type, full, meta) {
                var html ='<div class="form-check"><input class="form-check-input position-static SACheckBox" id="SA_' + full.ID + '" name="SA[' + full.ID + ']" type="checkbox" value="' + full.ID + '" ';
                if(full.StatusID == 1) html += ' checked '
                html += '></div>';
                return html;
            },
            "orderable": false, 
            "width": "2rem",
        },
        { "data": "ID",	"visible":false, "name":"ID" },
        { 'targets': 1, "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "150px",},
        { 'targets': 2, "data": "Name", "name":"Name", "title": "Name with Initials" },
        { 'targets': 3, "data": "IDNo",  "name": "IDNo" , "title": "ID No" }
        ]
    });

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        $('#SACheckAll').prop('checked',false);
        if($('#Batch').val() !== "" && $('#Year').val() !== ""){
            table.ajax.reload();
            // $('#current_sub').val($('#Subject').val());
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

    $('#exportExcel').on('click',function(e){
        e.preventDefault();
        var year = $('#Year').val();
        var regulation = $('#Regulation').val();
        var checkedValues = $('input.SACheckBox:checked').map(function(){return $(this).val();}).get();

        if(year !='' && regulation !='' && checkedValues != ''){
            window.open('/admin/registration/download-vle-export?year='+year+'&regulation='+regulation+'&studentIds='+checkedValues, '_blank');
        }

        
    });

});

</script>
@endsection




