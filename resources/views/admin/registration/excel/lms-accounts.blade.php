<table>
    <thead>
        <tr>
            <th>username</th>
            <th>password</th>
            <th>firstname</th>
            <th>lastname</th>
            <th>idnumber</th>
            <th>email</th>
            @for($i=1; $i<=$maxSubCount; $i++)
                <th>course{{$i}}</th>
            @endfor

        </tr>
    </thead>
    <tbody>
        @if($data)
            @foreach($data as $key=>$row)
                <tr>
                    <td>{{ $row['username']}}</td>
                    <td>{{ $row['password']}}</td>
                    <td>{{ $row['firstname']}}</td>
                    <td>{{ $row['lastname']}}</td>
                    <td>{{ $row['idnumber']}}</td>
                    <td>{{ $row['email']}}</td>
                    @foreach($row['subjects'] as $s)
                        <td>{{ $s }}</td>
                    @endforeach
                </tr>
            @endforeach
        @endif
    </tbody>
</table>