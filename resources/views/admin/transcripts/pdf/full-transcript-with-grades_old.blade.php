<!DOCTYPE HTML>
<html lang='en'>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />        
        <style>
            @page { margin:0px}
            table{width: 100%}
            body{font-family: Helvetica, Arial, Sans-Serif;}
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
        <script type="text/php">
            // setup
            $GLOBALS['start_pages'] = array( );
            $GLOBALS['current_start_page'] = null;
            $GLOBALS['show_page_numbers'] = false;
        </script>
        @foreach($data as $student)
        <script type="text/php">
            $GLOBALS['current_start_page'] = $pdf->get_page_number();
            $GLOBALS['start_pages'][$pdf->get_page_number()] = array(
            'show_page_numbers' => true,
            'page_count' => 1
            );
        </script>

        <p style="width:100%;text-align:center">
            <img src="{{public_path('images/pdf_header.png')}}" width="100%" >
        </p>
        <hr/>
        <p class="tac" style="font-size:1.2em;font-weight:bold">Academic Transcript</p>
        <table style="width:100%;border:0px;">
            <tr>
                <td colspan="2">Name: <b>{{$student['full_name']}}</b></td>
                <td>Registration No: <b>{{$student['registration_no']}}</b></td>
            </tr>
            <tr>
                <td colspan="2">Degree Awarded: <b>Bachelor of Science in Agriculture</b></td>
                <td>Student Index No: <b>{{$student['index_no']}}</b></td>
            </tr>
            <tr>
                <td colspan="2">Field of Specialization: <b>{{$student['specialization']}}</b></td>
                <td>Medium of Instruction: <b>English</b></td>
            </tr>
            <tr>
                <td width="43%">Level of Performance: <b>{{$student['class']}}</b></td>
                <td width="24%">F.G.P.A: <b>{{round($student['gpa'],2)}}</b></td>
                <td width="33%">Effective Date: <b>{{$student['effective_date']}}</b></td>
            </tr>
        </table>
        <hr/>
        <p><b>Detail Results</b></p>
        <table style="width:100%;border:0px;font-size:10px">
            <?php
            foreach($subjects as $year=>$semesters){
                $mainRow = '<tr>';
                    foreach($semesters as $semester=>$subArray){
                        $sm = ($semester%2==0)?2:1;
                        $mainRow .= '<td style="width:50%;vertical-align: top"><p style="text-align:center;margin:0;padding:0"><b>Semester '.$year.$sm.'00</b></p>';
                        $mainRow .= '<table class="bordered" style="width:100%;border:0px;"><tr><th width="65%">Subject</th><th width="14%">S. Code</th><th width="11%">Credits</th><th width="10%">Grade</th></tr>';
                        if($semester < $optSubSemStart){
                            foreach($subArray as $sid=>$subject){
                                $mainRow .= '<tr><td>'.$subject['name'].'</td><td style="text-align:center">'.$subject['code'].'</td><td style="text-align:center">'.$subject['credits'].'</td><td style="text-align:center">';
                                $mainRow .=  (isset($student['results'][$sid]))?$student['results'][$sid]['grade']:'-';
                                $mainRow .= '</td></tr>';
                            }
                        }else{
                            foreach($subArray as $sid=>$subject){
                                if(isset($student['results'][$sid])){
                                    $mainRow .= '<tr><td>'.$subject['name'].'</td><td style="text-align:center">'.$subject['code'].'</td><td style="text-align:center">'.$subject['credits'].'</td><td style="text-align:center">'.$student['results'][$sid]['grade'].'</td></tr>';
                                }
                            }
                        }
                        $mainRow .= '</table></td>';
                    }
                $mainRow .= '</tr>';
                echo $mainRow;
            }
            ?>
        </table>
        <table style="width:100%;vertical-align: top;margin-top:50px">
            <tr><td width="50%"></td><td class="tac">.............................................<br/>Assistant Registrar<br/>Faculty of Agriculture</td></tr>
        </table>

        <hr/>
        <p class="tac" style="font-size:12px"><b><u>GUIDES ON ACADEMIC TRANSCRIPT</u></b></p>

        <p><b><u>Specialization Modules:</u></b> The last 3 semesters (beginning from semester 3200) are spent in specialization Modules. Core courses are covered during first five semesters (semester 1100 – 3100). </p>
        <p><b><u>Final GPA (FGPA):</u></b> The Final Grade Point Average (FGPA) will be calculated considering the GPA obtained during year I(1000 series) year II(2000 series), year III(3000 series) and year IV(4000 series)in the total number of credit unit in each year.</p>

        <p><b><u>Level of Performance:</u></b></p>
        <table style="width:70%;vertical-align:top;margin-left:25px">
            <tr><th class="tal"><b><u>FGPA</u></b></th><th class="tal"><b><u>Level of Performance</u></b></th></tr>
            <tr><td><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAtElEQVRIie3UsQkCQRCF4U8RE8HIxAaMtAG7sAlbsAVbsAVbEEQUwcjIGkyMBBMR7gxuhQsUxNsNhHswwcLu/LM7+4ZaFbTGCZNUgA3yEHuMYwNamOJcAq0wig3qYIZrgDywQD82qIc57gF0C+tubNAAS2QBdFHcsB0bNFY0/9Wf7btNzdjUGEr2RMmanOybfjLasGpiaOAg4aho4CjxsPtvlR36bezeJfrk5OyHovIfztTiCdYiP+ZDvqZPAAAAAElFTkSuQmCC" width="10px" height="10px" style="margin-top:2px" />3.70</td><td>First Class</td></tr>
            <tr><td>3.30 – 3.69</td><td>Second Class (Upper Division)</td></tr>
            <tr><td>3.00 – 3.29</td><td>Second Class (Lower Division)</td></tr>
            <tr><td>2.00 – 2.99</td><td>Pass</td></tr>
        </table>

        <p><b><u>Credit:</u></b> 1 credit is equivalent of 15 hours of theory or 30 hours of practicals or a combination of theory & practicals.</p>


        <p><b><u>Key to Grades: </u></b></p>
        <table style="width:70%;vertical-align:top;margin-left:25px">
            <tr><th><b><u>Grade</u></b></th><th><b><u>Marks</u></b></th><th><b><u>Grade Point</u></b></th><th><b><u>Grade</u></b></th><th><b><u>Marks</u></b></th><th><b><u>Grade Point</u></b></th></tr>
            <?php
            $rows = '';
            $gradeCount = count($grades);
            for($i=0;$i<$gradeCount;$i++){
                $rows .= '<tr>';
                if($grades[$i]->upper_mark_limit<100 &&  $grades[$i]->lower_mark_limit > 0) {
                    $rows .='<td class="tac">'.$grades[$i]->grade.'</td><td class="tac">'.round($grades[$i]->upper_mark_limit,0).' – '.round($grades[$i]->lower_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                }elseif($grades[$i]->upper_mark_limit==100){ 
                    $rows .= '<td class="tac">'.$grades[$i]->grade.'</td><td class="tac"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAtElEQVRIie3UsQkCQRCF4U8RE8HIxAaMtAG7sAlbsAVbsAVbEEQUwcjIGkyMBBMR7gxuhQsUxNsNhHswwcLu/LM7+4ZaFbTGCZNUgA3yEHuMYwNamOJcAq0wig3qYIZrgDywQD82qIc57gF0C+tubNAAS2QBdFHcsB0bNFY0/9Wf7btNzdjUGEr2RMmanOybfjLasGpiaOAg4aho4CjxsPtvlR36bezeJfrk5OyHovIfztTiCdYiP+ZDvqZPAAAAAElFTkSuQmCC" width="10px" height="10px" style="margin-top:2px" />'.round($grades[$i]->lower_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                }else{
                    $rows .= '<td class="tac">'.$grades[$i]->grade.'</td><td class="tac"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAzklEQVRIie3VoU5DMRSH8d8gC8kEEs8MDhR2QWL2CnsFXgE5tyCxyPlJLG5uLzAzMzFBsmS5gYu4TdbchITcnGLgc02ar+3p6b/880vcYVZCfIEXfKLGfZS4hwm2SbzHI84i5Dd4S+Iar7iKEA8wRZXEG80pQhhjncQVnnAeIR5i4ViOJW4jxH084D2Jd2l8GiEfYeW467mmHTtx8sN5va4LfEfREuVcKnTJbfI2/cCzoDbNGWji4KDAQ8u5VigqcoqGXU6xuG5T7MP5A3wBJHA5DtEdNLIAAAAASUVORK5CYII=" width="10px" height="10px" style="margin-top:1px"/>'.round($grades[$i]->upper_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                } 
                $i++;
                if($grades[$i]->upper_mark_limit<100 &&  $grades[$i]->lower_mark_limit > 0) {
                    $rows .='<td class="tac">'.$grades[$i]->grade.'</td><td class="tac">'.round($grades[$i]->upper_mark_limit,0).' – '.round($grades[$i]->lower_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                }elseif($grades[$i]->upper_mark_limit==100){ 
                    $rows .= '<td class="tac">'.$grades[$i]->grade.'</td><td class="tac"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAtElEQVRIie3UsQkCQRCF4U8RE8HIxAaMtAG7sAlbsAVbsAVbEEQUwcjIGkyMBBMR7gxuhQsUxNsNhHswwcLu/LM7+4ZaFbTGCZNUgA3yEHuMYwNamOJcAq0wig3qYIZrgDywQD82qIc57gF0C+tubNAAS2QBdFHcsB0bNFY0/9Wf7btNzdjUGEr2RMmanOybfjLasGpiaOAg4aho4CjxsPtvlR36bezeJfrk5OyHovIfztTiCdYiP+ZDvqZPAAAAAElFTkSuQmCC" width="10px" height="10px" style="margin-top:2px" />'.round($grades[$i]->lower_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                }else{
                    $rows .= '<td class="tac">'.$grades[$i]->grade.'</td><td class="tac"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAAzklEQVRIie3VoU5DMRSH8d8gC8kEEs8MDhR2QWL2CnsFXgE5tyCxyPlJLG5uLzAzMzFBsmS5gYu4TdbchITcnGLgc02ar+3p6b/880vcYVZCfIEXfKLGfZS4hwm2SbzHI84i5Dd4S+Iar7iKEA8wRZXEG80pQhhjncQVnnAeIR5i4ViOJW4jxH084D2Jd2l8GiEfYeW467mmHTtx8sN5va4LfEfREuVcKnTJbfI2/cCzoDbNGWji4KDAQ8u5VigqcoqGXU6xuG5T7MP5A3wBJHA5DtEdNLIAAAAASUVORK5CYII=" width="10px" height="10px" style="margin-top:1px"/>'.round($grades[$i]->upper_mark_limit,0).'</td><td class="tac">'.round($grades[$i]->grade_point,1).'</td>';
                } 
                $rows .= '</tr>';
            }
            echo $rows;
            ?>
        </table>

        <p><b><u>GPA:</u></b> Grade Point Average is given for each individual semester.</p>

        <p><b><u>Semester:</u></b> A period of 15 weeks First and Second digits indicate the year and the term, respectively. Eg: Semester 3100 indicates the first term in year 3.</p>

        <p><b><u>Research Project:</u></b> A full time individual research project, undertaken over a period of one semester, including a formal presentation and report.</p>

        <p><b><u>Verification of Transcript:</u></b> If required, please send the transcript, or copy of transcript, to the contact address/numbers given on the front page.</p>
        

        <script type="text/php">
            // record total number of pages for the section
            $GLOBALS['start_pages'][$GLOBALS['current_start_page']]['page_count'] = $pdf->get_page_number() - $GLOBALS['current_start_page'] + 1;
        </script>
        <p style="page-break-before: always;"> </p>
        @endforeach


        <script type="text/php">
            $pdf->page_script('
              if ($pdf) {
                if (array_key_exists($PAGE_NUM, $GLOBALS["start_pages"])) {
                  $GLOBALS["current_start_page"] = $PAGE_NUM;
                  $GLOBALS["show_page_numbers"] = $GLOBALS["start_pages"][$GLOBALS["current_start_page"]]["show_page_numbers"];
                }
                if ($GLOBALS["show_page_numbers"]) {
                    $x = ($pdf->get_width() - 20) / 2;
                    $y = 800;
                    $text = "- Page ".($PAGE_NUM - $GLOBALS["current_start_page"] + 1)." -";
                    $font = $fontMetrics->get_font("helvetica", "bold");
                    $size = 6;
                    $pdf->text($x, $y, $text, $font, $size);
                }
              }
            ');
        </script>
    </body>
</html>