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

            if($rows->isEmpty()){
                return;
            }

            $code = null;
            $col = 0;
            $subjectRow = $rows[0];
            $subjects = [];
            do{
                $code = isset($subjectRow[(3 + ($col*4))]) ? strtoupper(trim($subjectRow[(3 + ($col*4))])) : null;
                if(!empty($code)){
                    $code = $code;
                    $subjects[$col + 1] = $code;
                    $col++;
                }else $code = null;
            }while(!empty($code));
            unset($rows[0]);
            unset($rows[1]);

            // print_r($subjects);dd();

            foreach ($rows as $rkey=>$row){
                $registration_no = strtoupper(trim($row[1] ?? ''));
                if($registration_no != ''){
                    foreach($subjects as $key=>$subject){
                        $mark = trim($row[(3 + (($key-1)*4))] ?? '');
                        $result = strtoupper(trim($row[(4 + (($key-1)*4))] ?? ''));

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
