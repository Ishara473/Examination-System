@extends('layouts.admin')

@section('title')
Print Final Transcripts
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Transcripts</a></li>
<li class="breadcrumb-item">Final Transcripts</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
    <div class="card-body">
        <form>
            <h4 class="text-secondary mb-3 mt-3"><i data-feather="printer" class="mr-0 pb-1"></i> Print Final Transcripts</h4>
            <div class="row" id="filterSection">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Batch" style="width:100%">Batch</label></label>
                            <select class="form-control d-inline" id="Batch" name="Batch">
                          @foreach($batches as $key=>$batch)
                                <option value="{{$key}}">{{$batch}}</option>
                          @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-md-3">
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
                        <th style="padding:4px" ><div class="form-check"><input class="form-check-input position-static " id="SACheckAll"  type="checkbox" value="1" /></div></th>
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
                            <button class="btn btn-success btn-grey-overlay btn-icon-split" id="BtnDownload" name="BtnDownload"><span class="icon"><i class="fa fa-print"></i></span> <span class="text text-left">Download Transcript</span></button>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 ">
                        <div class="form-group">
                            <label for="SubmittedTo">Submit To</label>
                            <textarea class="form-control" id="SubmittedTo" name="SubmittedTo" rows="6"></textarea>
                        </div>
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
    
    $('#Batch').val("");


    var table = $('#grid').DataTable({
        "dom": 'Blt',
        "paging": false,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
                "url": "{{url("/admin/transcripts/final-transcripts")}}?type=json",
                "data" : function ( d ) {
                    d.batch = $('#Batch').val();
                    d.search = $('#Search').val();
                    d.regulation = $('#Regulation').val();

                },
        },
    "order": [[ 2, "asc" ]],
        "columns": [
                {
                    'targets': 0,
                    "data": "Action",'class' : 'text-center pl-0',
                    render: function (data, type, full, meta) {
                        var html ='<div class="form-check"><input class="form-check-input position-static SACheckBox" id="SA_' + full.ID + '" name="SA[' + full.ID + ']" type="checkbox" value="' + full.ID + '" /></div>';
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
        var regulation = parseInt($('#ProcessingRegulation').val());
        var students = $(".SACheckBox:checked").map(function(){return $(this).val();}).get();
        var submitto = $('#SubmittedTo').val();
        if(students.length > 0) window.location = "{{url('/admin/transcripts/final-transcripts-download')}}?students="+students+'&regulation='+regulation+'&submitto='+submitto;
    });


});

</script>
@endsection




