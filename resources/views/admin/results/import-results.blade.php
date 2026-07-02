@extends('layouts.admin')

@section('title')
Import Results
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/results/view-uploaded-results')}}">Results Management</a></li>
<li class="breadcrumb-item">Results Import</li>
@endsection

@section('custom-css')
@endsection

@section('content')
<div class="card mb-1">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="file-text" class="mr-0 pb-1"></i> Import Student Exam Results</h4>
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
                            Upload a <strong>Excel</strong> file with the fields in the given order as shown below. 
                        </p>
                        <p class="ml-3">[Registration Number] , [Acc Year (Ex: 2020)] , [Subject Code] , [Marks (Ex: 50.00)] , [Grade (Ex: A,B,C+,AB,MCA,NE )]</p>
                    </div>
                </div>
            </div>
        </diV>       
        
        {{ Form::open(['url'=>url('/admin/results/upload-results'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
        <div class="row">
            <div class="col-md-5 col-lg-4">
                <label> Result File </label>
                <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="list" name="list" accept='.xlsx,.csv'>
                      <label class="custom-file-label" for="list" id="list_label">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="subject">Processing Subject</label>
                    <select id="subject" name="subject" class="form-control" required>
                        <option value="" selected></option>
                        @if($subjects)
                            @foreach($subjects as $row)
                                <option value="{{$row->Code}}">{{ $row->Name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-lg-3">
                <div class="form-group">
                    {!!Form::label('update', 'Update Existing Records',['class'=>'d-block'])!!}
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('update','0', false, ['class'=>'custom-control-input','id'=>'update_0']) !!}
                        {!!Form::label('update_0', 'No',['class'=>'custom-control-label'])!!}
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        {!! Form::radio('update','1', true, ['class'=>'custom-control-input','id'=>'update_1']) !!}
                        {!!Form::label('update_1', 'Yes',['class'=>'custom-control-label'])!!}
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-lg-2">
                <div class="form-group">
                    <label class="d-block">&nbsp; </label>
                    <button type="submit" class="btn btn-primary btn-grey-overlay btn-sm btn-icon-split" id="btnFormSubmit"><span class="icon"><i data-feather="upload"></i></span><span class="text">Upload Records</span></button>              
                </div>  
            </div>
        </div>
        {{ Form::close() }}        
        <div class="d-none" id="processUploadSection">
            {{ Form::open(['url'=>url('/admin/results/process-upload-results'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmProcess']) }}
            <div class="col-md-12"><hr/></div>
            <input type="hidden" name="processingSubject" id="processingSubject" value="" />
            <div class="row">
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                        <label class="d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-danger btn-grey-overlay btn-sm btn-icon-split" id="btnProcess"><span class="icon"><i data-feather="refresh-cw"></i></span><span class="text">Process Records</span></button>              
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
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    

    $('#list').change(function(e){
        var fileName = e.target.files[0].name;
        $('#list_label').html(fileName);
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
                if(data['status']>=0){
                    $('#processingSubject').val($('#subject').val());
                    $.get('{{url('/admin/results/get-uploaded-results')}}',function(data){ 
                        var html = '';
                        html += '<tr><th>registration no</th>><th>Year</th><th>Subject Code</th><th>Marks</th><th>Grade</th></tr>';             
                        $.each(data,function( key, value ) {
                            html += '<tr>';
                            html += '<td>'+value.registration_no+'</td>';
                            html += '<td>'+value.year+'</td>';
                            html += '<td>'+value.subject_code+'</td>';
                            html += '<td>'+value.marks+'</td>';
                            html += '<td>'+value.result+'</td>';
                            html += '</tr>';
                        });
                        html += '';
                        $('#uploadSample').html(html);
                        $('#processUploadSection').removeClass('d-none');
                    });
                }else{
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: data['msg'],
                        showConfirmButton: false,
                        timer: 10000,
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

    $('#frmProcess').on('submit',function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Upload?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, upload!'
            }).then((result) => {
            if(result.value) {
                $.ajax({
                    url : $(this).attr("action"),
                    type: "POST",
                    data : $(this).serialize(),
                    success:function(data, textStatus, jqXHR){
                        if(data['status']>0){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'File was Uploaded',
                                showConfirmButton: false,
                                timer: 3000,
                                width: 250,
                                toast:true,
                            });
                        }else{
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: data['msg'],
                                showConfirmButton: false,
                                timer: 3000,
                                width: 300,
                                toast:true,
                            });
                        }

                        $('#list_label').html('Choose file');
                        $('#list').val('');
                        $('#subject').val('');
                        $('#processingSubject').val('');
                        $('#uploadSample').html('');
                        $('#processUploadSection').addClass('d-none');
                    
                    }
                });
            }else{
                location.reload(); 
            }
        });
        
    });

    
/**/
});
</script>
@endsection