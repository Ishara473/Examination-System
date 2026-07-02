<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempTranferredList;
use Log;

class TranferImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        try{
            foreach ($rows as $row){
                $registration_no = strtoupper(trim($row[0]));
                if($registration_no!='') $data = TempTranferredList::create(['registration_no' => $registration_no]);
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
