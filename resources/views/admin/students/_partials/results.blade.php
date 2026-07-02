<h5 class="alert bg-gray-700 text-white">Examination Results</h5>

<div class="row">
    <div class="col-lg-12 col-md-12">
    @if($results)
        <nav id="results-tab">
            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                @foreach($results as $semester=>$result)
                <a class="nav-item nav-link" id="nav-r{{$semester}}-tab" data-toggle="tab" href="#nav-r{{$semester}}" role="tab" aria-controls="nav-r{{$semester}}" aria-selected="false">Semester {{$semester}}</a>
                @endforeach
            </div>
        </nav>
        <div class="tab-content" id="results-nav-tabContent">
        @foreach($results as $semester=>$result)
            <div class="tab-pane fade" id="nav-r{{$semester}}" role="tabpanel" aria-labelledby="nav-r{{$semester}}-tab">
                <table style="width:100%" class="table mb-3">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Code</th>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($result as $row)
                            <tr>
                                <td>{{$row['year']}}</td>
                                <td>{{$row['code']}}</td>
                                <td>{{$row['name']}}</td>
                                <td>{{$row['marks']}}</td>
                                <td>{{$row['result']}}</td>
                            </tr>
                        @endforeach
                    <tbody>
                </table>
            </div>       
        @endforeach
        </div>
    @endif
    </div>
</div>