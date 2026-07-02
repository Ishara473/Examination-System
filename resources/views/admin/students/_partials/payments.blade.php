<h5 class="alert bg-gray-700 text-white">Year Registration Payments</h5>

<div class="row">
    <div class="col-lg-12 col-md-12">
@if($payments)
        <table style="width:100%" class="table table-compact mb-3">
            <thead>
                <tr>
                    <th width="25%">Year</th>
                    <th width="25%" class="text-center">Study Year</th>
                    <th width="25%" class="text-center">Paid Amount</th>
                    <th width="25%" class="text-center">Hostel Requested</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $row)
                    <tr>
                        <td>{{$row['academic_year']}}</td>
                        <td class="text-center">{{$row['registered_year']}}</td>
                        <td class="text-right">{{$row['total_paid_amount']}}</td>
                        <td class="text-center">{{($row['need_hostel']==1)?'Yes':'No'}}</td>
                    </tr>
                @endforeach
            <tbody>
        </table>
@endif
    </div>
</div>