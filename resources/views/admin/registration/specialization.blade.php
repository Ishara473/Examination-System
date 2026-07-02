@extends('layouts.admin')

@section('title')
Process Yearly Specialization Requests
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">Registration Management</li>
<li class="breadcrumb-item">Process Specialization</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-1">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="upload" class="mr-0 pb-1"></i> Import Specialization Selection</h4>
        <diV class="alert bg-gray-200 mt-3">
            <a href="#" class="float-right " data-toggle="collapse" data-target="#instructions" >
                <i data-feather="chevron-down"></i>
            </a>
            <h4 class="text-info mb-1"><i class="fa fa-comments"></i> Instructions</h4>
           <div id="instructions" class="collapse">
                <hr class="mb-1 mt-0 border-info"/>        
                <div class="row">            
                    <div class="col-md-12">
                        <p class="mb-0">
                            Upload a <strong>Excel</strong> file with the fields in the order shown below. 
                        </p>
                        <p class="ml-3">[Registration Number], [Specialization]</p>
                    </div>
                </div>
            </div>
        </diV>
        {{ Form::open(['url'=>url('/admin/registration/upload-specialization'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
            <div class="row">
                <div class="col-md-8 col-lg-4">
                    <label> Selection File </label>
                    <div class="input-group">
                        <div class="custom-file">
                        <input type="file" class="custom-file-input" id="list" name="list" accept='.xlsx,.csv'>
                        <label class="custom-file-label" for="list" id="list_label">Choose file</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                        <label class="d-block">&nbsp; </label>
                        <button type="submit" class="btn btn-primary btn-grey-overlay btn-icon-split" id="btnFormSubmit"><span class="icon"><i class="fa fa-upload"></i></span><span class="text">Upload Records</span></button>              
                    </div>  
                </div>
            </div>
        {{ Form::close() }}
      </div>
    </div>
<div class="card mb-1 ">
    <div class="card-body">
        <form>
            <h4 class="text-secondary mb-3 mt-3"><i data-feather="download" class="mr-0 pb-1"></i> Export Specialization Requests</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Search">Registration No</label>
                        <input type="input" class="form-control" id="Search" name="Search">
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
                        <label for="BtnFilterSubmit" class="d-block">&nbsp;</label>
                        <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnFilterSubmit" name="BtnFilterSubmit"><span class="icon"><i class="fa fa-search"></i></span> <span class="text">Get Requests</span></button>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-3" >
            <div class="col-md-10 col-lg-10">
                <table id="grid" class="table table-striped compact table-bordered dataTable">
                </table>
            </div>
            <div class="col-md-2 col-lg-2 mt-1">
                <div class="row">
                    <div class="col-md-12 col-lg-12 ">
                        <div class="form-group">
                            <label for="GroupSize">Head Limit per Specialization</label>
                            <input type="input" class="form-control" id="GroupSize" name="GroupSize" value="{{$groupSize}}">
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 ">
                        <div class="form-group">
                            <a href="/admin/registration/download-specialization?year={{settings('year')}}&GroupSize={{$groupSize}}" class="btn btn-primary btn-grey-overlay btn-icon-split" id="BtnDownload" name="BtnDownload"><span class="icon"><i class="fa fa-download"></i></span> <span class="text">Download</span></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

</div>
</div>
@endsection



@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
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
    
    var table = $('#grid').DataTable({
		"dom": 'tip',
		"lengthMenu": [50],
		"responsive": true,
		"processing": true,
		"serverSide": true,
        "orderable": false,
        // "deferLoading": 0,
		"ajax": {
				"url": "{!! url('/admin/registration/process-specialization') !!}",
                "data" : function ( d ) {
                    d.type = 'json';
                    d.search = $('#Search').val();
                    d.year = $('#Year').val();
                },
		},
    "order": [[ 3, "desc" ]],
		"columns": [
        {
            "data": "Action",'class' : 'text-center',
            render: function (data, type, full, meta) {
                var html ='<a href="/admin/student/view/' + full.ID + '" class="btn btn-primary btn-xs view-app" data-id="'+full.ID +'"><i class="fa fa-search"></i></a> &nbsp;';
                return html;
            },
          "orderable": false, 
          "width": "5rem",
		},
        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "RegistrationNo", "name": "RegistrationNo", "title": "Registration No", "width": "120px"},
        { "data": "GPA", "name": "GPA", "title": "2nd Year GPA" },
        { "data": "Name", "name":"Name", "title": "Name with Initials","orderable": false,},
        { "data": "Option1", "name":"Option1", "title": "Option 1", "orderable": false, },
        { "data": "Option2", "name":"Option2", "title": "Option 2", "orderable": false, },
        { "data": "Option3", "name":"Option3", "title": "Option 3", "orderable": false, },
        // { "data": "Option4", "name":"Option4", "title": "Option 4", "orderable": false, "width": "100px", },
        // { "data": "Option5", "name":"Option5", "title": "Option 5", "orderable": false, "width": "100px", },
        // { "data": "Option6", "name":"Option6", "title": "Option 6", "orderable": false, "width": "100px", },
        // { "data": "Option7", "name":"Option7", "title": "Option 7", "orderable": false, "width": "100px",  },
        // { "data": "Option8", "name":"Option8", "title": "Option 8", "orderable": false, "width": "100px", },
				
		]
	});

    $('#BtnFilterSubmit').on('click',function(e){
        e.preventDefault();
        table.ajax.reload();
        $('#BtnDownload').attr('href','/admin/registration/download-specialization?year='+$('#Year').val()+'&GroupSize='+$('#GroupSize').val());
    });

    $('#list').change(function(e){
        var fileName = e.target.files[0].name;
        $('#list_label').html(fileName);
    });


    $('#GroupSize').on('change',function(e){
        e.preventDefault(0);
        $('#BtnDownload').attr('href','/admin/registration/download-specialization?year='+$('#Year').val()+'&GroupSize='+$('#GroupSize').val());
    });
  

    $('#frmUpload').on('submit',function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Upload?',
            text: "The students current specialization will be overridden by the one in the file!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, upload!'
            }).then((result) => {
            if (result.value) {
                var formData = new FormData(this);
                $.ajax({
                    url : $(this).attr("action"),
                    type: "POST",
                    data : formData,
                    processData: false,
                    contentType: false,
                    success:function(data, textStatus, jqXHR){
                        if(data==1){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'File was Uploaded',
                                showConfirmButton: false,
                                timer: 3000,
                                width: 250,
                                toast:true,
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        //if fails     
                    }
                });
            }
        });

        
    });
    

});
</script>
@endsection