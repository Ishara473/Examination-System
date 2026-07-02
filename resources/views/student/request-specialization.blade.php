@extends('layouts.student')

@section('custom-css')
<link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<h2 class="mb-1">Course Specialization Request</h2>
<hr class="mt-0 mb-5"/>

@if(count($std_sp_requests)>0)
<div class="group-section">
    <h4 class="text-primary">Requested Course Specialization</h4>
    <table class="table table-sm table-borderless mb-0">
        <thead >
            <tr>
                <th class="text-left">Departments</th>
                <th class="text-left">Specialization Modules</th>
                <th class="text-center">Order of Preferences</th>
            </tr>
        </thead>
        <tbody>
            @foreach($std_sp_requests as $spRequest)
            <tr>
                <td>{{ $specializations[$spRequest->specialization_id]->department }}</td>
                <td>{{ $specializations[$spRequest->specialization_id]->name }}</td>
                <td class="text-center">{{$spRequest->preference_order}}</td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@else
{{ Form::open(['url'=>'/student/request-specialization','method'=>'post','id'=>'formSpecializationSelection']) }} 
<div class="group-section">
    <h4 class="text-primary">Course Specialization</h4>
    <div class="alert alert-warning">
        To continue your program, you should have a course specialization. Please <strong>Please select 3 specializations</strong> you wish to follow and give the <strong>preference order</strong> of your choice.
    </div>
    <table class="table table-sm table-borderless mb-0">
        <thead >
            <tr>
                <th class="text-center">Select</th>
                <th>Departments</th>
                <th>Specialization Modules</th>
                <th>Order of Preferences</th>
            </tr>
        </thead>   
        @foreach($specializations as $specialization)
            <tr>
                <td class="text-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input specialization_selection_checkbox" data-id="{{ $specialization->id }}" id="select_specialization_{{ $specialization->id }}">
                        <label class="custom-control-label" for="select_specialization_{{ $specialization->id }}"></label>
                      </div>
                </td>
                <td>{{ $specialization->department }}</td>
                <td>{{ $specialization->name }}</td>
                <td>
                <select class="form-control form-control-sm d-block sp_selection" name="specialization[{{ $specialization->id }}]" id="specialization_{{ $specialization->id }}" disabled >
                    <option value=""></option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<div class="text-right">
    <button type="submit" class="btn btn-success" id="btnSubmitForm">Submit Request</button>
</div>
{{ Form::close() }}   
@endif

@endsection

@section('custom-js')
<script src="{{ asset('plugins/validate/jquery.validate.min.js') }}"></script>
<script>
$(document).ready(function() {

    $('.sp_selection').on('change',function(e){
        var $current = $(this);
        if($(this).val() != ''){
            $('select[name^="specialization"]').each(function() {
                if ($(this).val() == $current.val() && $(this).attr('id') != $current.attr('id')) { 
                    $(this).val('');
                    $(this).trigger('focus');
                }
            });
        }
    });


    $('.specialization_selection_checkbox').on('click',function(e){
        var id = $(this).data('id');
        if($(this).prop("checked") == true){
            $('#specialization_'+id).prop('disabled', false);
            $('#specialization_'+id).prop('required', true);
        }else{
            $('#specialization_'+id).prop('disabled', true);
            $('#specialization_'+id).val('');
            $('#specialization_'+id).prop('required', false);
        }
    });

    var validator = $('#formSpecializationSelection').validate({
        errorClass: 'input-error text-danger text-small',
    });


    $('#btnSubmitForm').on('click',function(e){
        e.preventDefault();
        if($('#formSpecializationSelection').valid()){
            $.post("{{url('/student/request-specialization')}}",$('#formSpecializationSelection').serialize(),function(data){
                if(data==1) location.reload();
            });
        }
    });
});
</script>

@endsection
