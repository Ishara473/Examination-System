@extends('layouts.admin')

@section('title')
Import Students
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/student">Student management</a></li>
<li class="breadcrumb-item">Import</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-1 ">
    <div class="card-body">
        <h4 class="text-secondary mb-3 mt-3"><i data-feather="user" class="mr-0 pb-1"></i> Import New Students</h4>

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
                            Upload a <strong>Excel</strong> file with the fields in the given order as shown below. Make sure the <strong>dates are in iso format (YYYY-MM-DD)</strong> format<br/>
                            RUSL File :
                        </p>
                        <p class="ml-3">[Registration Number],[Enr. Status],[Date Enr. Reg.],[NIC],[StudentNameFull],[Title],[Last Name],[Initials],[Gender],[AddressLN1],[AddressLN2],[AddressLN3],[Admin. District],[Medium],[Telephone],[Telephone Home],[Telephone1],[Student E-Mail],[StudentALIndexNo],[StudentALZ_Score]</p>
                        
                    </div>
                </div>
            </div>
        </div>       
        
        {{ Form::open(['url'=>url('/admin/student/upload'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmUpload']) }} 
        <div class="row">
            <div class="col-md-4 col-lg-2">
                <div class="form-group">
                    {!!Form::label('type', 'File Type')!!}
                    {!!Form::select('type', [2=>'RUSL File'], 2, ['class' => 'form-control','id'=>'type'])!!}   
                </div>  
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="form-group">
                    {!!Form::label('batch', 'Batch')!!}
                    {!!Form::select('batch',$batches, '', ['class' => 'form-control','id'=>'batch'])!!} 
                </div>  
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="form-group">
                    {!!Form::label('regulation', 'Regulation')!!}
                    {!!Form::select('regulation',$regulations, $curRegulation, ['class' => 'form-control','id'=>'regulation'])!!} 
                </div>  
            </div>
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
        <div class=" d-none" id="processUploadSection">
            {{ Form::open(['url'=>url('/admin/student/process-uploaded'),'method'=>'post','enctype'=>'multipart/form-data','id'=>'frmProcess']) }}
            <div class="row">
                <div class="col-md-12"><hr/></div>
                <div class="col-md-4 col-lg-3">
                    <div class="form-group">
                        {!!Form::label('insert_type', 'Record Creating Method')!!}
                        {!!Form::select('insert_type', [1=>'Add New Records Only',2=>'Add New & Update Existing Records'], 1, ['class' => 'form-control','id'=>'insert_type'])!!} 
                        <input type="hidden" name="processType" id="processType" value=""/>  
                    </div>  
                </div>
                <div class="col-md-4 col-lg-2">
                    <div class="form-group">
                        <label class="d-block">&nbsp;</label>
                        <div class="spinner-border" role="status"  style="display:none" id="btnbsyProcessing">
                            <span class="sr-only">Loading...</span>
                          </div>
                        <button type="submit" class="btn btn-danger btn-sm btn-icon-split" id="btnProcess"><span class="icon"><i data-feather="refresh-cw"></i></span><span class="text">Process Records</span></button>              
                    </div>  
                </div>
            </div>
            {{ Form::close() }}
            <div class="row mb-3">
                <div class="col-md-12">
                    <table id="uploadSample" style="font-size:.7rem;width:100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('custom-js')
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert/sweetalert2.all.min.js') }}"></script>

<script>
function loadUplodedData(){

    
}

$(document).ready(function() {
    $('.yearpicker').datepicker({
        format:'yyyy',
        viewMode: "years", 
        minViewMode: "years"
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    

    $('#student_list').change(function(e){
        var fileName = e.target.files[0].name;
        $('#student_list_label').html(fileName);
    });

    // $('#show_advance_options').prop('checked',false);

    // $('#show_advance_options').change(function() {
    //     if($(this).prop('checked')){
    //         $('#advance_options').fadeIn();
    //     }else{
    //         $('#advance_options').fadeOut();
    //     }
    // });

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
                    var type = $('#type').val();
                    $('#processType').val(type);
                    $.post('{{url('/admin/student/get-uploaded-list')}}',{type:type},function(data){
                        if(type == 2){
                            
                            var html = '';
                            html += '<tr><th>registration no</th> <th>status</th> <th>registration date</th> <th>nic</th> <th>full name</th> <th>title</th> <th>name marking</th> <th>initials</th> <th>gender</th> <th>address1</th> <th>address2</th> <th>address3</th> <th>district</th> <th>medium</th> <th>mobile</th> <th>phone1</th> <th>phone2</th> <th>email</th> <th>al index no</th> <th>zscore</th></tr>';             
                            $.each(data.records,function( key, value ) {
                                html += '<tr>';
                                html += '<td>'+value.registration_no+'</td>';
                                html += '<td>'+value.status+'</td>';
                                html += '<td>'+value.registration_date+'</td>';
                                html += '<td>'+value.nic+'</td>';
                                html += '<td>'+value.full_name+'</td>';
                                html += '<td>'+value.title+'</td>';
                                html += '<td>'+value.name_marking+'</td>';
                                html += '<td>'+value.initials+'</td>';
                                html += '<td>'+value.gender+'</td>';
                                html += '<td>'+value.address1+'</td>';
                                html += '<td>'+value.address2+'</td>';
                                html += '<td>'+value.address3+'</td>';
                                html += '<td>'+value.district+'</td>';
                                html += '<td>'+value.medium+'</td>';
                                html += '<td>'+value.mobile+'</td>';
                                html += '<td>'+value.phone1+'</td>';
                                html += '<td>'+value.phone2+'</td>';
                                html += '<td>'+value.email+'</td>';
                                html += '<td>'+value.al_index_no+'</td>';
                                html += '<td>'+value.zscore+'</td>';
                                html += '</tr>';
                            });
                            html += '';
                        }
                        if(data.recordCount>10)html += '<tr><td colspan="20" class="text-center">...</td></tr><tr><td colspan="20" class="text-center">Total Uploaded Records : '+data.recordCount+'</td></tr>';
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
        $('#btnProcess').addClass('disabled').prop('disabled','true');
        // $('#btnbsyProcessing').show();

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
                }else{
                    // $('#btnProcess').show();
                    $('#btnProcess').removeClass('disabled').removeProp('disabled');


                }
            }
        });
    });
    

});
</script>
@endsection