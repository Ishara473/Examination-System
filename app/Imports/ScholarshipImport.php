<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempScholarshipImport;

use Carbon\Carbon;
use Log;


class ScholarshipImport implements ToCollection
{
    private $scholaship_type;

    function __construct($scholaship_type) 
    {
        $this->scholaship_type = $scholaship_type;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        try{
            foreach ($rows as $key=>$row){
                $registration_no = strtoupper(trim($row[0]));
                if($registration_no!=''){
                    $data = TempScholarshipImport::create([
                        'registration_no' => $registration_no,
                        'student_id'=>0,
                        'awarded_date' => ($row[1] - 25569) * 86400,
                        'scholarship_type' => $this->scholaship_type
                    ]);
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
