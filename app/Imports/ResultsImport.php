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
                if($registration_no != '' && $result != ''){
                $data = TempResultsImport::create([
                    'registration_no' => $registration_no,
                    'year' => intval($row[1]),
                    'subject_code' => trim($row[2]),
                    'marks'=>$row[3],
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
