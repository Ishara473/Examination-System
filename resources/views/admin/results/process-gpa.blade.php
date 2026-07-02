@extends('layouts.admin')

@section('title')
Process GPA
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/results/view-uploaded-results')}}">Results management</a></li>
<li class="breadcrumb-item">Export GPA</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
<div class="card-body">
    <form>
    <h4 class="text-secondary mb-3 mt-3"><i data-feather="download" class="mr-0 pb-1"></i> Export GPA</h4>
    <div class="row" id="filterSection">
        <div class="col-md-2">
            <div class="form-group">
                <label for="StudyYear">Current Study Year</label>
                <select id="StudyYear" name="StudyYear" class="form-control">
                    <option value=""></option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="Regulation">Regulation</label>
                <select id="Regulation" name="Regulation" class="form-control">
                    @foreach($regulations as $regulation)
                        <option value="{{$regulation->id}}" @if($regulation->is_current==1) selected="selected" @endif>{{$regulation->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="Search">Registration No</label>
                <input type="input" class="form-control" id="Search" name="Search">
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="form-group">
                <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Get Students</span></button>
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
        <div class="col-md-2 col-lg-2 mt-1">
            <div class="row">
                <div class="col-md-12 col-lg-12 ">
                    <div class="form-group">
                        <input type="hidden" name="Regulation" id="ProcessingRegulation" value="0"/>
                        <label for="ProcessingSemester">GPA Processing Semester</label>
                        <select id="ProcessingSemester" name="ProcessingSemester" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="0">Final</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12 ">
                    <div class="form-group">
                        <button class="btn btn-success btn-grey btn-icon-split" id="BtnDownload" name="BtnDownload"><span class="icon"><i class="fa fa-download"></i></span> <span class="text">Export to Excel</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('custom-js')
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script>
$(document).ready(function() {
    
    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        $('#ProcessingRegulation').val($('#Regulation').val());
        table.ajax.reload();
    });
    
    $('#Semester').val("");


    var table = $('#grid').DataTable({
        "dom": 'Blt',
        "paging": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
    // "deferLoading": 0,
        "ajax": {
                "url": "{{url("/admin/results/process-gpa")}}?type=json",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.year = $('#StudyYear').val();
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
                { "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "150px",},
                { "data": "Name", "name":"Name", "title": "Name with Initials" },
                { "data": "Batch", "title": "Batch", "name":"Batch"  },
                { "data": "IDNo",  "name": "IDNo" , "title": "ID No" }			                
        ]
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
        var semester = parseInt($('#ProcessingSemester').val());
        var regulation = parseInt($('#ProcessingRegulation').val());
        var students = $(".SACheckBox:checked").map(function(){return $(this).val();}).get();
        if(students.length > 0) window.location = "{{url('/admin/results/download-raw-gpa')}}?students="+students+"&semester="+semester+"&regulation="+regulation;
    });


});

</script>
@endsection




