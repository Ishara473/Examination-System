@extends('layouts.admin')

@section('title')
Import Students Documents
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/student">Student management</a></li>
<li class="breadcrumb-item">Import Documents</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-1">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="user" class="mr-0 pb-1"></i> Import Student Profile Documents</h4>  
        <div class="alert bg-gray-200 mt-3">
            <a href="#" class="float-right " data-toggle="collapse" data-target="#instructions" >
                <i data-feather="chevron-down"></i>
            </a>
            <h4 class="text-info mb-1"><i class="fa fa-comments"></i> Instructions</h4>
           <div id="instructions" class="collapse">
                <hr class="mb-1 mt-0 border-info"/>        
                <div class="row">            
                    <div class="col-md-12">
                        <p class="mb-0">
                            The document should be pdf and should be named after the the students registration number (ex AG2018001.pdf). Upload a <strong>pdf</strong> file to upload a single file or compressed <strong>Zip</strong> file to Upload multiple files. <b>Do not zip a folder and upload.</b> instead select all the required files and zip. <b> Max File Size is 50MB</b>
                        </p>                        
                    </div>
                </div>
            </div>
        </div>
        {{ Form::open(['url'=>url('/admin/student/upload-documents'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
        <div class="row">
            <div class="col-md-8 col-lg-4">
                <label> File List</label>
                <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="file" name="file" accept='.pdf,.zip'>
                      <label class="custom-file-label" for="file" id="file_label">Choose file </label>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="form-group">
                    <label class="d-block">&nbsp; </label>
                    <button type="submit" class="btn btn-primary btn-grey-overlay btn-sm btn-icon-split" id="btnFormSubmit"><span class="icon"><i data-feather="upload"></i></span><span class="text">Upload Files</span></button>              
                </div>  
            </div>
        </div>
        {{ Form::close() }}        
    </div>
</div>
@endsection



@section('custom-js')
{{-- <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
function loadUplodedData(){

    
}

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    

    $('#file').change(function(e){
        var fileName = e.target.files[0].name;
        $('#file_label').html(fileName);
    });


    $('#frmUpload').on('submit',function(e){
        e.preventDefault();
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
                    $('#frmUpload')[0].reset();
                    $('#file_label').html('Choose file');
                }else{
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occured while tring to process the File.',
                        showConfirmButton: false,
                        timer: 5000,
                        width: 300,
                        toast:true,
                    });
                }
               
            },
            error: function(jqXHR, textStatus, errorThrown){
                //if fails     
            }
        });
    });    

});
</script>
@endsection