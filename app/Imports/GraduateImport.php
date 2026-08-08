<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempGraduateList;
use Log;

use Carbon\Carbon;

class GraduateImport implements ToCollection
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
                if(!empty($registration_no)){
                    $dateValue = $row[1] ?? null;
                    if (is_numeric($dateValue)) {
                        $date = ($dateValue - 25569) * 86400;
                    } else {
                        $date = strtotime($dateValue);
                    }
                    TempGraduateList::create([
                        'registration_no' => $registration_no,
                        'degree_effective_date' => $date ? date('Y-m-d', $date) : null,
                    ]);
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
