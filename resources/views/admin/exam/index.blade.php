@extends('layouts.admin')

@section('title')
Exam Applications
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">Exam management</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <form>
        <a href="#" class="float-right " data-toggle="collapse" data-target="#filterSection"  aria-expanded="true">
            <i data-feather="chevron-down"></i>
        </a>
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="edit-2" class="mr-0 pb-1"></i> View Exam Applications</h4>
        <div class="row collapse show" id="filterSection">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="Semester">Semester</label>
                    <select id="Semester" name="Semester" class="form-control">
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
            <div class="col-md-4 col-lg-4 text-right">
                <div class="form-group d-inline">
                    <label class="d-block">&nbsp;</label>
                    {{-- <button class="btn btn-success btn-icon-split" id="btnDownloadPDF" ><span class="icon"><i class="fa fa-file-pdf"></i></span> <span class="text">Download Applications</span></button> --}}
                    <button class="btn btn-info btn-grey-overlay btn-icon-split" id="btnDownload" ><span class="icon"><i class="fa fa-file-excel"></i></span> <span class="text">Export to Excel</span></button>
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
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {
    
    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
    });
    
    $('#Semester').val("");


    var table = $('#grid').DataTable({
        "dom": 'Bltip',
        "lengthMenu": [25, 50, 75, 100],
        "responsive": true,
        "processing": true,
        "serverSide": true,
    // "deferLoading": 0,
        "ajax": {
                "url": "/admin/exam/list",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.semester = $('#Semester').val();
                },
        },
    "order": [[ 2, "asc" ]],
        "columns": [
        {
                    "data": "Action",'class' : 'text-center',
                    render: function (data, type, full, meta) {
                        var html ='<a href="/admin/exam/application/' + full.ID + '" target="_blank" class="btn btn-primary btn-xs view-app" data-id="'+full.ID +'"><i class="fa fa-file-alt"></i></a> &nbsp;';
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
        ]
    });

    $('#btnDownload').on('click',function(e){
        e.preventDefault();
        if(parseInt($('#Semester').val())>0) window.location = "{{url('/admin/exam/export-to-excel')}}?semester="+$('#Semester').val()+'&search='+$('#Search').val();
    });

    // $('#btnDownloadPDF').on('click',function(e){
    //     e.preventDefault();
    //     if(parseInt($('#Semester').val())>0) window.location = "{{url('/admin/exam/print-applications')}}?type=pdf&semester="+$('#Semester').val()+'&search='+$('#Search').val();
    // });


});

</script>
@endsection




