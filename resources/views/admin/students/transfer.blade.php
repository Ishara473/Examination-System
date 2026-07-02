@extends('layouts.admin')

@section('title')
Transfer Students
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/student">Student management</a></li>
<li class="breadcrumb-item">Transfer</li>
@endsection

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="card mb-1 ">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="user" class="mr-0 pb-1"></i> Remove Transferred Students</h4>
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
                            Upload a <strong>Excel</strong> list containing the tranferred students registration no. <span class="text-danger">Warning : All The information related the given registration numbers will be removed permenantly from the system.</span>
                        </p>                       
                    </div>
                </div>
            </div>
        </div>       
        
        {{ Form::open(['url'=>url('/admin/student/transfer'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
        <div class="row">
            <div class="col-md-8 col-lg-4">
                <label> File List</label>
                <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="student_list" name="student_list" accept='.xlsx,.csv'>
                      <label class="custom-file-label" for="student_list" id="student_list_label">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="form-group">
                    <label class="d-block">&nbsp; </label>
                    <button type="submit" class="btn btn-primary btn-grey-overlay btn-sm btn-icon-split" id="btnFormSubmit"><span class="icon"><i data-feather="upload"></i></span><span class="text">Upload Records</span></button>              
                </div>  
            </div>
        </div>
        {{ Form::close() }}        

        <div class="d-none" id="processUploadSection">
            {{ Form::open(['url'=>url('/admin/student/process-transfer'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmProcess']) }}
            <hr/>
            <div class="row">
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-sm btn-icon-split" id="btnProcess"><span class="icon"><i data-feather="refresh-cw"></i></span><span class="text">Remove Stduents for the System</span></button>              
                    </div>  
                </div>
            </div>
            {{ Form::close() }}
            <div class="row">
                <div class="col-md-12">
                    <table id="uploadSample" class="table"></table>
                </div>
            </div>
        </div>
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
    

    $('#student_list').change(function(e){
        var fileName = e.target.files[0].name;
        $('#student_list_label').html(fileName);
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
                if(typeof data.errors !== 'undefined'){
                    console.log(data.errors);
                }else{
                    $.get('{{url('/admin/student/get-transferred-list')}}',function(data){ 
                        var html = '';
                        html += '<tr><th>registration no</th></tr>';             
                        $.each(data.records,function( key, value ) {
                            html += '<tr>';
                            html += '<td>'+value.registration_no+'</td>';
                            html += '</tr>';
                        });
                        html += '';
                        $('#uploadSample').html(html);
                        $('#processUploadSection').removeClass('d-none');
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                //if fails     
            }
        });
    });

    $('#frmProcess').on('submit',function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Deletion?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!'
            }).then((result) => {
            if(result.value) {
                $.ajax({
                    url : $(this).attr("action"),
                    type: "POST",
                    data : $(this).serialize(),
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
                            location.reload(); 
                        }
                    }
                });
            }else{
                location.reload(); 
            }
        });
        
    });
    

});
</script>
@endsection