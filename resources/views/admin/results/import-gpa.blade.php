@extends('layouts.admin')

@section('title')
Import GPA
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/results/view-uploaded-results')}}">Results Management</a></li>
<li class="breadcrumb-item">Import GPA</li>
@endsection

@section('custom-css')
@endsection

@section('content')
<div class="card mb-1">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="upload" class="mr-0 pb-1"></i> Import Student Year GPA</h4>
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
                        <p class="ml-3">[Registration Number] , [GPA]</p>
                    </div>
                </div>
            </div>
        </diV>       
        
        {{ Form::open(['url'=>url('/admin/results/upload-gpa'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
        <div class="row">
            <div class="col-md-8 col-lg-4">
                <label> Result File </label>
                <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="list" name="list" accept='.xlsx,.csv'>
                      <label class="custom-file-label" for="list" id="list_label">Choose file</label>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="ProcessingSemester">GPA Processing Semester</label>
                    <select id="ProcessingSemester" name="ProcessingSemester" class="form-control" required>
                        <option value="" selected></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="9">Year 2 Cumulative</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="0">Final Year</option>
                    </select>
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
        
        Swal.fire({
            title: 'Confirm Upload?',
            text: "You won't be able to revert this!",
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
                            $('#list_label').html('Choose file');
                            $('#list').val('');
                            $('#ProcessingSemester').val('');                    
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