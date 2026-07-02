@extends('layouts.admin')

@section('title')
Print Exam Applications
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/admin/exam">Exam management</a></li>
<li class="breadcrumb-item">Print</li>
@endsection

@section('custom-css')
<link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="card mb-2">
<div class="card-body">
    <form>
    <h4 class="text-secondary mb-3 mt-3"><i data-feather="printer" class="mr-0 pb-1"></i> Print Admissions</h4>
    <div class="row" id="filterSection">
        <div class="col-md-2">
            <div class="form-group">
                <label for="Semester">Semester</label>
                <select id="Semester" name="Semester" class="form-control">
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
                <label for="Search">Registration No / NIC</label>
                <input type="input" class="form-control" id="Search" name="Search">
            </div>
        </div>
        <div class="col-md-7">
            <div class="form-group d-inline">
                <label class="d-block">&nbsp;</label>
                <button class="btn btn-primary btn-grey-overlay btn-icon-split" id="btnDownloadProperPDF" ><span class="icon"><i class="fa fa-print"></i></span> <span class="text">Proper Admissions</span></button>
                <button class="btn btn-info btn-grey-overlay btn-icon-split" id="btnDownloadRepeatPDF" ><span class="icon"><i class="fa fa-print"></i></span> <span class="text">Repeat Admissions</span></button>
            </div>
        </div>
    </div>
    </form>
</div>
</div> 
@endsection


@section('custom-js')
{{-- <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script> --}}
<script>
$(document).ready(function() {
    
    $('#Semester').val("");

    $('#btnDownloadRepeatPDF').on('click',function(e){
        e.preventDefault();
        if(parseInt($('#Semester').val())>0) window.location = "{{url('/admin/exam/print-applications')}}?type=R&semester="+$('#Semester').val()+'&search='+$('#Search').val();
    });

    $('#btnDownloadProperPDF').on('click',function(e){
        e.preventDefault();
        if(parseInt($('#Semester').val())>0) window.location = "{{url('/admin/exam/print-applications')}}?type=P&semester="+$('#Semester').val()+'&search='+$('#Search').val();
    });


});

</script>
@endsection




