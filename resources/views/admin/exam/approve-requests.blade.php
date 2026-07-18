@extends('layouts.admin')

@section('title')
Approve Subjects Requests
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/exam">Exam management</a></li>
<li class="breadcrumb-item">Approve Subjects</li>
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
    <h4 class="text-secondary mb-3 mt-3"><i data-feather="edit-2" class="mr-0 pb-1"></i> Approve Subjects</h4>
    <div class="row" id="filterSection">
        <div class="col-md-2">
            <div class="form-group">
                <label for="Regulation">Regulation</label>
                <select id="Regulation" name="Regulation" class="form-control">
                    <option value="" selected></option>
                    @foreach($regulations as $r)
                        <option value="{{$r->id}}">{{$r->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
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
        <div class="col-md-3">
            <div class="form-group">
                <label for="Subject">Subject</label>
                <select id="Subject" name="Subject" class="form-control">
                    <option value="" selected></option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="Search">Registration No / NIC</label>
                <input type="input" class="form-control" id="Search" name="Search">
            </div>
        </div>
        <div class="col-md-2 col-lg-3">
            <div class="form-group">
                <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Get Applications</span></button>
            </div>
        </div>
    </div>
    </form>
    {{-- <hr/> --}}
    <div class="row mt-5">
        <div class="col-md-6 text-secondary">
            <h5> Subject : <span id="subjectName"></span><input type="hidden" name="current_sub" id="current_sub" value=""></h5>
        </div>
        <div class="col-md-6 text-right">
            <a href="" class="btn btn-success btn-icon-split btn-sm" id="updateSelection"><span class="icon"><i class="fa fa-pencil-alt"></i></span> <span class="text">update</span></a>
        </div>
    </div>
    <div class="row mt-1 mb-3">
        <div class="col-md-12">
            <table id="grid" class="table table-striped compact table-bordered dataTable">
                <thead>
                    <th style="padding:4px"><div class="form-check"><input class="form-check-input position-static " id="SACheckAll"  type="checkbox" value="1"></div></th>
                    <th></th>
                    <th>RegistrationNo</th>
                    <th>Name</th>
                    <th>IDNo</th>
                    <th>Status</th>
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


    var table = $('#grid').DataTable({
        "dom": 'Bltip',
        // "lengthMenu": [25, 50, 75, 100],
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "paging":false,
        "deferLoading": 0,
        "ajax": {
                "url": "/admin/exam/approve-by-subject",
                "data" : function ( d ) {
                    d.type='json';
                    d.search = $('#Search').val();
                    d.subjectId = $('#Subject').val();
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
        { 'targets': 3, "data": "IDNo",  "name": "IDNo" , "title": "ID No" },
        { 'targets': 4, "data": "Status", "title": "Status", "name":"Status"  }
        ]
    });

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        $('#SACheckAll').prop('checked',false);
        if($('#Subject').val() !== ""){
            table.ajax.reload();
            $('#subjectName').html($('#Subject option:selected').html());
            $('#current_sub').val($('#Subject').val());
        }
    });

    $('#Regulation').val("");
    $('#Regulation').on('change',function(e){
        $('#Semester').val("");
        $('#Subject').html('<option value=""></option>').val("");
    });

    $('#Semester').val("");
    $('#Semester').on('change',function(e){
        e.preventDefault();
        if($(this).val()!=''){
            $.get('/admin/exam/get-subjects',{semester:$(this).val(),regulation:$('#Regulation').val()},function(responce){
                var options = '<option value=""></option>';
                $.each(responce, function(index){
                    options += '<option value="'+responce[index].ID+'">'+responce[index].Name+'</option>';
                });
                $('#Subject').html(options);
                $('#Subject').val("");

            });
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

    $('#updateSelection').on('click',function(e){
        e.preventDefault();
        var checkedValues = $('input.SACheckBox:checked').map(function(){return $(this).val();}).get();
        var uncheckedValues = $('input.SACheckBox:not(:checked)').map(function(){return $(this).val();}).get();
        $.post('/admin/exam/approve-app-subject',{Approved:checkedValues, Pending:uncheckedValues, Subject:$('#current_sub').val() },function(e){
            table.ajax.reload();
        });
        
    });

});

</script>
@endsection




