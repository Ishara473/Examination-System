
<?php $subjectCount = count($subjects);?>
<style>
table, th, td{border:1px solid #666666; border-collapse: collapse}
</style>
<table>
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            @foreach($subjects as $subject)
                <th style="background-color:#eeeeee;border:1px solid #000000" colspan="2">{{$subject['code']}}</th>
                <th style="background-color:#eeeeee;border:1px solid #000000">Credit</th>
                <th style="background-color:#eeeeee;border:1px solid #000000">{{$subject['credits']}}</th>
            @endforeach
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th style="background-color:#eeeeee;border:1px solid #000000">Name With Initials</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">Registration No</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">Index No</th>
            @foreach($subjects as $subject)
                <th style="background-color:#eeeeee;border:1px solid #000000">Marks</th>
                <th style="background-color:#eeeeee;border:1px solid #000000">Grade</th>
                <th style="background-color:#eeeeee;border:1px solid #000000">GP</th>
                <th style="background-color:#eeeeee;border:1px solid #000000">GP*CV</th>
            @endforeach
            <th style="background-color:#eeeeee;border:1px solid #000000">Total GP Value</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">Total Credits</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">AB</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">MCA</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">NE</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">F</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">GPA</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">GPA</th>
            <th style="background-color:#eeeeee;border:1px solid #000000">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $student)
            <?php
                $MCA=0;
                $AB=0;
                $F=0;
                $NE=0;
                $TotGPV=0;
                $counter=0;
                $TotCredits=0;      
            ?>
            <tr>
                <td style="border:1px solid #000000">{{$student['full_name']}}</td>
                <td style="border:1px solid #000000">{{$student['registration_no']}}</td>
                <td style="border:1px solid #000000">{{$student['index_no']}}</td>
                @foreach($subjects as $key=>$row)
                    <?php $counter++;?>
                    @if(isset($student['results'][$key]))
                        <?php 
                            $grade = $student['results'][$key]['grade']; 
                            $type = $student['results'][$key]['type']; 
                        ?>
                        <td style="border:1px solid #000000">{{$student['results'][$key]['marks']}}</td>
                        <td style="border:1px solid #000000">{{$grade}}</td>
                        <td style="border:1px solid #000000">{{$grades[$grade]}}</td>
                        <td style="border:1px solid #000000">@if($type>0){!! $grades[$grade]*$row['credits'] !!}@else 0 @endif</td>
                        <?php
                            if($type>0){
                                $TotGPV += $grades[$grade]*$row['credits'];
                                $TotCredits += $row['credits'];
                            }
                            if($grade=='AB') $AB +=1;
                            elseif($grade=='MCA') $MCA +=1;
                            elseif($grade=='F') $F +=1;
                            elseif($grade=='NE') $NE +=1;
                        ?>
                    @else
                        <td style="border:1px solid #000000">0</td>
                        <td style="border:1px solid #000000">0</td>
                        <td style="border:1px solid #000000">0</td>
                        <td style="border:1px solid #000000">0</td>
                    @endif
                @endforeach
                <td style="border:1px solid #000000">{{$TotGPV}}</td>
                <td style="border:1px solid #000000">{{$TotCredits}}</td>
                <td style="border:1px solid #000000">{{$AB}}</td>
                <td style="border:1px solid #000000">{{$MCA}}</td>
                <td style="border:1px solid #000000">{{$NE}}</td>
                <td style="border:1px solid #000000">{{$F}}</td>
                <td style="border:1px solid #000000">@if($AB!=0 && $MCA==0 && $NE==0 && $F==0 && $TotCredits != 0) {{ $TotGPV / $TotCredits }} @else - @endif</td>
                <td style="border:1px solid #000000">@if($AB==0 && $MCA==0 && $NE==0 && $F==0 && $TotCredits != 0) {{ round($TotGPV / $TotCredits,2) }} @else - @endif</td>
                <td style="border:1px solid #000000">-</td>
            </tr>
        @endforeach
    </tbody>
</table>