<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\TempGPAImport;
use Log;

class GPAImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        unset($collection[0]);
        try{
            foreach ($collection as $key=>$row){
                $registration_no = strtoupper(trim($row[0]));
                if(is_numeric($row[1]) && $registration_no != ''){
                    $data = TempGPAImport::create([
                        'registration_no' => $registration_no,
                        'gpa' => floatval($row[1])
                    ]);
                }
                
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
