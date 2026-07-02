<table>
    <thead>
        <tr>
            <th>Registration No</th>
            <th>Student Name</th>
            <th>2nd Year GPA</th>
            <th>Option 1</th>
            <th>Option 2</th>
            <th>Option 3</th>
            <th>Proposed Option</th>
            <th>Current Option</th>
        </tr>
    </thead>
    <tbody>
        @if($data)
        @foreach($data as $key=>$row)
            <tr>
                <td>{{ $row['RegistrationNo']}}</td>
                <td>{{ $row['Name']}}</td>
                <td>{{ $row['GPA']}}</td>
                <td>{{ $row['Option1']}}</td>
                <td>{{ $row['Option2']}}</td>
                <td>{{ $row['Option3']}}</td>
                <td>{{ $row['ProposedOption']}}</td>
                <td>{{ $row['CurrentOption']}}</td>
            </tr>
        @endforeach
        @endif
    </tbody>
</table>