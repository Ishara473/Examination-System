@extends('layouts.admin')

@section('title')
Fees
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{url('/admin/settings')}}">Settings</a></li>
<li class="breadcrumb-item">Fees</li>
@endsection

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('plugins/datatables/datatables-boostrap4.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<div class="card border-left-danger">
    <div class="card-body">
        <table id="grid" class="table table-striped compact table-bordered dataTable">
        </table>
    </div>
</div>


@endsection


@section('custom-js')
{{-- <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script> --}}
<script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('plugins/validate/jquery.validate.js') }}"></script>
<script>
$(document).ready(function() {
    
    var table = $('#grid').DataTable({
		"dom": 'Bltip',
		"lengthMenu": [10, 25, 50, 75, 100],
		"responsive": true,
		"processing": true,
		"serverSide": true,
    // "deferLoading": 0,
		"ajax": {
				"url": "{{url('/admin/settings/fees')}}",
                "data" : function ( d ) {
                    d.search = $('#Search').val();
                    d.type = 'json';
                },
		},
        "order": [[ 2, "asc" ]],
		"columns": [
        {
		    "data": "Action", 'class' : 'text-center',
			render: function (data, type, full, meta) {
                var html ='<a href="#" class="btn btn-primary btn-xs edit-record" data-id="'+full.ID +'"><i class="fa fa-pencil-alt "></i></a> &nbsp;';
                // html +='<a href="#" class="btn btn-danger btn-sm delete-record" data-id="'+full.ID +'"><i class="fa fa-trash-alt"></i></a>';
                return html;
			},
            "orderable": false, 
            "width": "5rem",
		},
        { "data": "ID",	"visible":false, "name":"ID" },
        { "data": "Code", "visible":false,  "name": "Code", "title": "Code","width": "15rem", },
        { "data": "Name", "name":"Name", "title": "Name" },
        { "data": "Amount", "title": "Amount", "name":"Amount","width": "15rem",  },
        { "data": "Surchage",  "name": "Surchage" , "title": "Surchage Amount","width": "15rem", }
		]
    });
    
    $('body').on('click','.edit-record',function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var data = table.row($(this).parents('tr')).data();
        $('#id').val(id);
        $('#name').val(data['Name']);
        $('#amount').val(data['Amount']);
        $('#surcharge_amount').val(data['Surchage']);
        $('#editRecord').modal('show');
    });

    var validater = $('#editRecordFrom').validate({
        errorClass:'border-danger text-danger'
    });

    $('#BtnFormSubmit').on('click',function(e){
        e.preventDefault();
        if($('#editRecordFrom').valid()){
            $.post("{{url('/admin/settings/update-fees')}}",$('#editRecordFrom').serialize(),function(data){
                $('#editRecord').modal('hide');
                table.ajax.reload();
            });
        }

    });



});

</script>
@endsection


@section('modal')
<div class="modal fade" id="editRecord" role="dialog" aria-labelledby="editRecordTitle" >
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRecordTitle">Edit Fees</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['url' => '#', 'id'=>'editRecordFrom']) }}
                <input type="hidden" value="" name="id" id="id"/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                                {!!Form::label('name', 'Name')!!}
                                {!! Form::text('name','', ['class'=>'form-control','id'=>'name','required'=>'required', 'readonly'=>'readonly']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('amount', 'Amount')!!}
                                {!! Form::text('amount','', ['class'=>'form-control','id'=>'amount','required'=>'required']) !!}              
                        </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                                {!!Form::label('surcharge_amount', 'Surcharge Amount')!!}
                                {!! Form::text('surcharge_amount','', ['class'=>'form-control ','id'=>'surcharge_amount','required'=>'required']) !!}              
                        </div>  
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="BtnFormSubmit">Save changes</button>
                    </div>
                </div>
                {{ Form::close()}}
            </div>
        </div>
    </div>
</div>
@endsection