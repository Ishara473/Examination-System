@extends('layouts.student')

@section('custom-css')
{{-- <link href="{{ asset('plugins/datepicker/bootstrap-datepicker.standalone.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('content')
<h2 class="mb-1 text-grey">Results  </h2>
<hr class="mt-0 mb-3"/>
<div class="row">
    <div class="col-md-12">
        <div id="accordion">
            @if($results)
                @foreach($results as $semester=>$result)
                <div class="card mb-1">
                    <div class="card-header" id="heading_{{$semester}}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse_{{$semester}}" aria-expanded="true" aria-controls="collapse_{{$semester}}">
                                Semester {{$semester}}
                            </button>
                        </h5>
                    </div>                
                    <div id="collapse_{{$semester}}" class="collapse" aria-labelledby="heading_{{$semester}}" data-parent="#accordion">
                        <div class="card-body pb-0">
                            <table style="width:100%" class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Code</th>
                                        <th>Subject</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result as $row)
                                        <tr>
                                            <td>{{$row['year']}}</td>
                                            <td>{{$row['code']}}</td>
                                            <td>{{$row['name']}}</td>
                                            <td>{{$row['result']}}</td>
                                        </tr>
                                    @endforeach
                                <tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection

@section('custom-js')

@endsection
