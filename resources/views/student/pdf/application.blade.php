<!DOCTYPE HTML>
<html lang='en'>
<head>
    <style>
        table{width: 100%}
        td{padding:3px}
        .tar{text-align:right}
        .tac{text-align:center}
        .box{ border:1px solid #444;padding:4px}
        .bordered, .bordered td,.bordered th{ border: 1px solid #444; border-collapse: collapse}
        .round-border{border-radius: 15px}
    </style>
</head>
    <body>
        <p style="width:100%;text-align:center">
            <img src="{{public_path('images/logo.png')}}" height="100px"><br/>
            FACULTY OF AGRICULTURE<br/>
            RAJARATA UNIVERSITY OF SRI LANKA<br/>
            B.Sc. Agriculture (Special) Degree<br/>
            Year {{$application->acc_year}} Semester {{$application->semester}} Examinations – September / October {{$application->year}}<br/>
            Admission Card<br/>
        </p>

        <table style="width:100%;border:0px;">
        <tr>
            <td>Name of Candidate: {{$student->full_name}}</td>
            <td class="tar">Index Number : <span class="box">{{$student->index_no}}</span></td>
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
        <div style="margin:auto;width:50%">
            <table style="text-align:left" class="bordered">
                <tr><th>Course Code</th><th>Course Title</th></tr>
                @foreach ($subjects as $item)
                <tr><td>{{$item->code}}</td><td>{{$item->name}}</td>
                </tr>
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
        <p class="tac">ATTESTATION</p>
        <p>The candidate should sign this part in the presence of the Mentor of him or her</p>
        <div class="round-border" style="border:1px solid;padding:5px;margin:5px">
            <table>
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
        <p class="tar">Index No: {{$student->index_no}}</p>
        <p>Important:<br/>
            All applicants should return this admission card to the Supervisor / Invigilator after completing each examination.
        </p>
        <table class="bordered">
            <thead><tr><th width="10%">S.No</th><th width="10%">Date</th><th width="10%">Course Code</th><th width="50%">Course Title</th><th width="10%">Candidate’s Signature</th><th width="10%">Invigilator's Signature</th></tr></thead>
            @for($i=1;$i<30;$i++)
                <tr><td class="tac">{{$i}}</td><td></td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </table>
    </body>
</html>
