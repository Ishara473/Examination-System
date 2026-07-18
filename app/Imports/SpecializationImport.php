<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempSpecializationImport;
use Log;


class SpecializationImport implements ToCollection
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
                $specialization = trim($row[1]);
                if($registration_no != '' && $specialization != ''){
                    $data = TempSpecializationImport::create([
                        'registration_no' => $registration_no,
                        'specialization' => $specialization
                        ]);
                    }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
