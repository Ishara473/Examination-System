<!DOCTYPE HTML>
<html lang='en'>
<head>
    <style>
         @page { margin:2px 10px;}
        table{width: 100%}
        td{padding:3px}
        .tar{text-align:right}
        .tac{text-align:center}
        .box{ border:1px solid #444;padding:4px 6px}
        .bordered, .bordered td,.bordered th{ border: 1px solid #444; border-collapse: collapse}
        .no-break {page-break-inside: avoid;}
        .round-border{border-radius: 10px; border:1px solid;}
    </style>
</head>
    <body style="margin:0">
        @foreach($data as $student)
        <p style="width:100%;text-align:center">
            <img src="{{public_path('images/logo.png')}}" height="100px"><br/>
            FACULTY OF AGRICULTURE<br/>
            RAJARATA UNIVERSITY OF SRI LANKA<br/>
            {{$student['program']}} Degree<br/>
            @if($type=='P')Year <span class="box">{{$exam['year']}}</span> @endif Semester <span class="box">{{$exam['semester']}}</span> Examinations – {{$exam['period']}}<br/>
            Admission Card<br/>
        </p>

        <table style="width:100%;border:0px;">
        <tr>
            <td>Name of Candidate: {{$student['full_name']}}</td>
            <td class="tar">Index Number : <span class="box">{{$student['indexno']}}</span></td>
        </tr>
        </table>
        <hr/>
        Instructions:
        <ul>
        <li>Candidates should be in the outside of the Examination hall at least 15 minutes before the</li>
        commencement of Examination
        <li>No candidate is allowed to enter the examination hall without the Admission Card and Student Record
        book/ Student Identity Card.</li>
        <li>Any unauthorized materials will not be allowed.</li>
        <li>All signatures must be legibly signed in blue or black ink.</li>
            <li>Those who do not follow the instructions will not be eligible to sit the examination.</li>
        </ul>
        Applied Courses:
        <div style="margin:auto;width:70%">
            <table style="text-align:left" class="bordered">
                <tr><th>Course Code</th><th>Course Title</th></tr>
                @foreach ($student['subjects'] as $key=>$status)
                    {{-- @if($status == 1) --}}
                    <tr>
                        <td>{{$subjects[$key]['code']}}</td><td>{{$subjects[$key]['name']}}</td>
                    </tr>
                    {{-- @endif --}}
                @endforeach
            </table>
        </div>
        Special Note:
        <ul>
            <li>If any one of the above course is not eligible, it will be displayed on the Departmental Notice Boards.</li>
            <li>Please note that this is your responsibility to check with the relevant Department / Units before
                preparation of the Examination.</li>
        </ul>

        <table style="border-bottom:1px dashed #444">
            <tr>
                <td>Date: </td>
                <td class="tar">Assistant Registrar </td>
            </tr>
        </table>
        <div class=" no-break">
            <p class="tac">ATTESTATION</p>
            <p>The candidate should sign this part in the presence of his or her Mentor</p>
            
                <table class="round-border">
                    <tr><td colspan="2">Candidate’s Full Name: </td></tr>
                    <tr><td colspan="2">Candidate’s Signature:</td></tr>
                    <tr><td colspan="2">I certify that the applicant whose signature appears above is well known to me.</td></tr>
                    <tr><td>Signature of the Mentor:</td><td>Date:</td></tr>
                    <tr><td colspan="2">Name of the Mentor:</td></tr>
                    <tr><td>Designation:</td><td>Official Seal:</td></tr>
                    <tr><td colspan="2">Address:</td></tr>

                </table>
        </div>
        <p style="page-break-before: always"> </p>
        <p class="tar">Index No: {{$student['indexno']}}</p>
        <p>Important:<br/>
            All applicants should return this admission card to the Supervisor / Invigilator after completing each examination.
        </p>
        <table class="bordered">
            <thead><tr><th width="10%">S.No</th><th width="10%">Date</th><th width="10%">Course Code</th><th width="40%">Course Title</th><th width="10%">Candidate’s Signature</th><th width="10%">Invigilator's Signature</th></tr></thead>
            @for($i=1;$i<30;$i++)
                <tr><td class="tac">{{$i}}</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </table>
        <p style="page-break-before: always"> </p>
        @endforeach
    </body>
</html>
