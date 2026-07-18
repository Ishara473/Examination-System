<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempYearRegistrationImport;
use Log;


class YearRegistrationImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        try{
            foreach ($rows as $key=>$row){
                $registration_no = strtoupper(trim($row[0]));
                if($registration_no!= ''){
                    $data = TempYearRegistrationImport::create([
                        'registration_no' => $registration_no,
                        'year'=> $row[1],
                        'study_year' => $row[2],
                        'paid_amount' => (isset($row[3]))?$row[3]:0,
                        'hostel'=> (isset($row[4]) && $row[4]=='Y')?1:0,

                    ]);
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
