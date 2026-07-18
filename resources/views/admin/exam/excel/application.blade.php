<table>
    <thead>
        <tr>
            <th>Name With Initials</th>
            <th>Registration No</th>
            <th>Index No</th>
            <th>NIC</th>
            @foreach($subjects as $subject)
                <th>{{$subject}}</th>
                <th>Approval Status</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $student)
            <tr>
                <td>{{$student['name']}}</td>
                <td>{{$student['regno']}}</td>
                <td>{{$student['indexno']}}</td>
                <td>{{$student['idno']}}</td>
                @foreach($subjects as $key=>$val)
                    @if(isset($student['subjects'][$key]))
                        <td>Applied</td>
                        <td>{{($student['subjects'][$key]==1)?'Approved':'Pending'}}</td>
                    @else
                        <td>Not Applied</td>
                        <td> - </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>