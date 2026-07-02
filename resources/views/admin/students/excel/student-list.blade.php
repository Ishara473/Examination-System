<table>
    <thead>
        <tr>
            <th>Name With Initials</th>
            <th>Registration No</th>
            <th>Index No</th>
            <th>NIC</th>
            <th>Batch</th>
            <th>Study Year</th>
            <th>Specialization</th>
            {{-- <th>Phone</th>
            <th>eMail</th> --}}
            <th>Semester 1 GPA</th>
            <th>Semester 2 GPA</th>
            <th>Semester 3 GPA</th>
            <th>Semester 4 GPA</th>
            <th>Semester 5 GPA</th>
            <th>Semester 6 GPA</th>
            <th>Semester 7 GPA</th>
            <th>Semester 8 GPA</th>
            <th>Final GPA</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($data))
        @foreach($data as $student)
            <tr>
                <td>{{$student->Name}}</td>
                <td>{{$student->RegistrationNo}}</td>
                <td>{{$student->IndexNo}}</td>
                <td>{{$student->IDNo}}</td>
                <td>{{$student->Batch}}</td>
                <td>{{$student->StudyYear}}</td>
                <td>{{$specilization[$student->SpecializationId]}}</td>
                <td>{{$student->S1GPA}}</td>
                <td>{{$student->S2GPA}}</td>
                <td>{{$student->S3GPA}}</td>
                <td>{{$student->S4GPA}}</td>
                <td>{{$student->S5GPA}}</td>
                <td>{{$student->S6GPA}}</td>
                <td>{{$student->S7GPA}}</td>
                <td>{{$student->S8GPA}}</td>
                <td>{{$student->FinalGPA}}</td>
            </tr>
        @endforeach
        @endif
    </tbody>
</table>