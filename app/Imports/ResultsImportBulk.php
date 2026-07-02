<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


use App\TempResultsImport;
use Log;

class ResultsImportBulk implements ToCollection
{
    function __construct($user, $year) 
    {
        $this->user = $user;
        $this->year = $year;
    }


    public function collection(Collection $rows)
    {
        try{
            // dd(\json_encode($rows));

            $code = null;
            $col = 1;
            $subjectRow = $rows[0];
            $subjects = [];
            do{
                $code = isset($subjectRow[($col*4)])?strtoupper(trim($subjectRow[($col*4)])):null;
                if(!empty($code)){
                    $code = $code;
                    $subjects[$col] = $code;
                    $col++;
                }else $code = null;
            }while(!empty($code));
            unset($rows[0]);
            unset($rows[1]);

            // print_r($subjects);dd();

            foreach ($rows as $rkey=>$row){
                $registration_no = strtoupper(trim($row[2]));
                if($registration_no != ''){
                    foreach($subjects as $key=>$subject){
                        $mark = trim($row[($key*4)]);
                        $result = strtoupper(trim($row[($key*4)+1]));

                        if(($mark !== 0 && empty($mark)) || $result == '') continue;
                        else $mark = is_numeric($mark)?$mark:0;

                        $data = TempResultsImport::create([
                            'registration_no' => $registration_no,
                            'year' => $this->year,
                            'subject_code' => $subject,
                            'marks'=>$mark,
                            'result'=> $result,
                            'uploaded_by'=>$this->user
                        ]); 
                    }
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
