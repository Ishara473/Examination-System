<?php 
    $roman = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'V1',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];
?>
<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />        
        <style>
            @page { margin:0;}
            table{width: 100%}
            td{padding:1px;vertical-align: top}
            .tal{text-align:left}
            .tar{text-align:right}
            .tac{text-align:center}
            .box{ border:1px solid #444;padding:4px 6px}
            .bordered, .bordered td,.bordered th{ border: 1px solid #444; border-collapse: collapse}
            .no-break {page-break-inside: avoid;}
            .round-border{border-radius: 10px; border:1px solid;}
            hr{height:0px; }
            /* @page { margin: 180px 50px; } */
            /* #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; } */
            /* #footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; } */
            /* #footer .page:after { content: counter(page, upper-roman); } */
        </style>
    </head>
    <body style="margin:0;font-size:11px;">
        @foreach($data as $student)
        <p style="width:100%;text-align:center">
            <img src="{{public_path('images/pdf_header.png')}}" width="100%" >
        </p>
        <hr/>
        <p class="tar" style="font-size:10px">Date : {{$date }}</p>
        <p class="tac" style="font-size:1.4em;font-weight:bold;">FACULTY OF AGRICULTURE<br/>
            {{$examString}} - {{strtoupper($examDateString)}}<br/>
            @if($type!=1)
            (REPEAT/ MEDICAL)<br/>
            @endif
            {{$student['program']}}</p>
        <p> 
        @if($type==1)
        This is to inform you that you have completed the examination held in {{$examDateString}}. The grades obtained by you in each subject for which you have appeared at this
examination are given below.
        @else
        This is to inform you that you have followed the repeat/ medical subjects of the semester examination held in {{$examDateString}}. The grades obtained by you to subject for which you have appeared at this examination are given below.
        @endif
        </p>

        <table style="width:100%;border:0px;">
            <tr>
                <td colspan="2">Name: <b>{{$student['full_name']}}</b></td>
            </tr>
            <tr>
                <td colspan="2">Registration Number: <b>{{$student['registration_no']}}</b></td>
            </tr>
            <tr>
                <td colspan="2">Index Number: <b>{{$student['index_no']}}</b></td>
            </tr>
            <tr>
                <td colspan="2">Semester GPA : <b>{{$student['gpa']}}</b></td>
            </tr>
        </table>

        <?php $rows = 8;?>
        <table style="width:100%;border:0px;" class="bordered">
            <tr><th>Subject</th><th>subject Code</th><th>Grade</th></tr>
            @foreach($student['results'] as $result)
                <tr><td>{{$result['subject_name']}}</td><td class="tac">{{$result['subject_code']}}</td><td class="tac">{{$result['grade']}}</td></tr>
                <?php $rows--;?>
            @endforeach

        @if($rows>0)
            @for($i = 0; $i< $rows; $i++)
                <tr><td>&nbsp;</td><td></td><td></td></tr>
            @endfor
        @endif
        </table>

        <table style="width:100%;border:0px;font-size:10px;vertical-align:top">
            <tr>
                <td style="width:50%;vertical-align: top">
                    <table style="width:50%;vertical-align: top">
                        <tr><th>Grade</th><th>Marks</th><th>Grade Point</th></tr>
                        <?php
                        $rows = '';
                            foreach($grades as $grade){
                                if($grade->upper_mark_limit<100 &&  $grade->lower_mark_limit > 0) {
                                    $rows .= '<tr class="tac"><td>'.$grade->grade.'</td><td>'.round($grade->upper_mark_limit,0).' – '.round($grade->lower_mark_limit,0).'</td><td>'.round($grade->grade_point,1).'</td></tr>';
                                }elseif($grade->upper_mark_limit==100){
                                    $rows .= '<tr class="tac"><td>'.$grade->grade.'</td><td><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAtElEQVRIie3UsQkCQRCF4U8RE8HIxAaMtAG7sAlbsAVbsAVbEEQUwcjIGkyMBBMR7gxuhQsUxNsNhHswwcLu/LM7+4ZaFbTGCZNUgA3yEHuMYwNamOJcAq0wig3qYIZrgDywQD82qIc57gF0C+tubNAAS2QBdFHcsB0bNFY0/9Wf7btNzdjUGEr2RMmanOybfjLasGpiaOAg4aho4CjxsPtvlR36bezeJfrk5OyHovIfztTiCdYiP+ZDvqZPAAAAAElFTkSuQmCC" width="10px" height="10px" style="margin-top:2px" />'.round($grade->lower_mark_limit,0).'</td><td>'.round($grade->grade_point,1).'</td></tr>';
                                }else{
                                    $rows .= '<tr class="tac"><td>'.$grade->grade.'</td><td><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAzklEQVRIie3VoU5DMRSH8d8gC8kEEs8MDhR2QWL2CnsFXgE5tyCxyPlJLG5uLzAzMzFBsmS5gYu4TdbchITcnGLgc02ar+3p6b/880vcYVZCfIEXfKLGfZS4hwm2SbzHI84i5Dd4S+Iar7iKEA8wRZXEG80pQhhjncQVnnAeIR5i4ViOJW4jxH084D2Jd2l8GiEfYeW467mmHTtx8sN5va4LfEfREuVcKnTJbfI2/cCzoDbNGWji4KDAQ8u5VigqcoqGXU6xuG5T7MP5A3wBJHA5DtEdNLIAAAAASUVORK5CYII=" width="10px" height="10px" style="margin-top:1px"/>'.round($grade->upper_mark_limit,0).'</td><td>'.round($grade->grade_point,1).'</td></tr>';
                                }
                            }
                            echo $rows;
                        ?>
                    </table>
                </td>
                <td>
                    <table style="width:100%;vertical-align:top">
                        <tr><td class="tac">AB</td><td>absent</td></tr>
                        <tr><td class="tac">NE</td><td>not eligible</td></tr>
                        <tr><td class="tac">MCA</td><td>medical certificate approved</td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <table style="width:100%;vertical-align: top;margin-top:10px">
            <tr><td>Prepared by:........................</td><td class="tac">.............................................<br/>Assistant Registrar<br/>Faculty of Agriculture</td></tr>
            <tr><td colspan="2">Checked by:........................</td></tr>
        </table>

        <div style="position: absolute; bottom:0px">
            <hr/>
            <p style="width:80%;margin:auto">
            <img src="{{public_path('images/pdf_footer.png')}}" width="100%" >
            </p>
        </div>
        <p style="page-break-before: always;"> </p>
        @endforeach
    </body>
</html>