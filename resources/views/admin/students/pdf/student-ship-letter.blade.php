<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style>
            body{font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif; margin: 0; padding: 0;}
            @page { margin: 0; padding: 0;}
            table{width: 100%}
            td{padding:1px;vertical-align: top}
            .tar{text-align:right}
            .tal{text-align:left}
            .tac{text-align:center}
            .box{ border:1px solid #444;padding:4px 6px}
            .bordered, .bordered td,.bordered th{ border: 1px solid #444; border-collapse: collapse}
            .no-break {page-break-inside: avoid;}
            .round-border{border-radius: 10px; border:1px solid;}
            hr{height:0px; }
            .paragraph { text-align: justify; }
        </style>
    </head>
    <body style="margin:0;font-size:13px">
        <div style="position: relative; width: 100%; margin-top: 0;">
            <img src="{{public_path('images/student-ship-letter-header.png')}}" width="100%" style="display:block; margin: 0; padding: 0;">
            <div style="width:100%; border-top:2px solid #222;"></div>
        </div>

        <div style="width:76%; margin:40px auto 10px; font-family: 'Times New Roman', serif; font-size: 13px;">
            <div style="position: relative; top: -22px; width: 100%;">
                <div style="position: relative; width: 100%;">
                    <img src="{{public_path('images/student-ship-letter-labels.png')}}" width="100%" style="display: block;">
                    <span style="position: absolute; left: 14.2%; top: 3px; white-space: nowrap; font-size: 9pt; font-weight: normal;">{{$registration_no}}</span>
                    <span style="position: absolute; left: 78%; top: 0px; white-space: nowrap; font-size: 11pt; font-weight: normal;">{{$letter_date}}</span>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33%;"></td>
                    <td style="width: 33%; font-weight: bold;"></td>
                    <td style="width: 33%;"></td>
                </tr>
                </table>
            </div>
        </div>

        <div style="width:76%; margin:22px auto 24px; text-align:left; font-weight:bold; font-size:16px; text-decoration: underline; font-family: 'Book Antiqua', serif;">
            TO WHOM IT MAY CONCERN
        </div>

        <div style="width:76%; margin:0 auto 110px; text-align:justify; line-height:2.0; font-size:16px; font-family: 'Book Antiqua', serif;">
            <p class="paragraph" style="text-indent:0; margin:0;">
                This is to certify that {{$student_name}}, Registration Number {{$registration_no}} is an internal student of the Faculty of Agriculture, Rajarata University of Sri Lanka who is following {{$degree_program}} since {{$registration_date}}. The duration of the degree programme is {{$degree_duration}}. Presently, he is studying in the 1st Semester of the {{$current_year}} Year.
            </p>
            <p class="paragraph" style="text-indent:0; margin-top:30px;">
                This letter is issued on {{$student_name}}'s request.
            </p>
        </div>

        <div style="width:76%; margin:0 auto 30px; text-align:left;">
            <p style="margin-left:0; font-size:16px; font-family: 'Book Antiqua', serif; margin-bottom:15px;">
                Senior Assistant Registrar<br/><br/>
                Faculty of Agriculture
            </p>
        </div>

        <div style="position: absolute; bottom:0px; left:0px; right:0px;">
            <img src="{{public_path('images/student-ship-letter-footer.png')}}" width="100%" style="display:block;">
        </div>

    </body>
</html>
