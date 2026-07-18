<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempResultsImport;
use Log;


class ResultsImport implements ToCollection
{
    private $user ;

    function __construct($user) 
    {
        $this->user = $user;
    }


    public function collection(Collection $rows)
    {
        unset($rows[0]);
        try{
            foreach ($rows as $key=>$row){
                $registration_no = strtoupper(trim($row[0]));
                $result = strtoupper(trim($row[4]));
                $marks = floatval($row[3]);
                
                if($registration_no != '' && $result != ''){
                    // Validate marks range (0-100)
                    if($marks < 0 || $marks > 100){
                        Log::notice("Invalid marks {$marks} for registration {$registration_no}");
                        continue; // Skip invalid marks
                    }
                    
                    // Validate grade format
                    $validGrades = ['A', 'A+', 'A-', 'B', 'B+', 'B-', 'C', 'C+', 'F', 'AB', 'MCA', 'NE'];
                    if(!in_array($result, $validGrades)){
                        Log::notice("Invalid grade {$result} for registration {$registration_no}");
                        continue; // Skip invalid grades
                    }
                    
                $data = TempResultsImport::create([
                    'registration_no' => $registration_no,
                    'year' => intval($row[1]),
                    'subject_code' => trim($row[2]),
                    'marks'=>$marks,
                    'result'=> $result,
                    'uploaded_by'=>$this->user
                    ]);
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
