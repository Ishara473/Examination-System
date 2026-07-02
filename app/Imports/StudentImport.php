<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\TempStudentImport;

use Carbon\Carbon;
use Log;

class StudentImport implements ToCollection,  WithMultipleSheets
{
    private $batch;
    private $regulation_id ;


    function __construct($batch,$regulation_id) 
    {
        $this->batch = $batch;
        $this->regulation_id = $regulation_id;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
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
                    $data = TempStudentImport::create([
                                    'registration_no' => $registration_no,
                                    'status' => $row[1],
                                    'registration_date' => ($row[2] - 25569) * 86400,
                                    'nic' => strtoupper(trim($row[3])),
                                    'full_name' => ucwords(strtolower(trim($row[4]))),
                                    'title' => $row[5],
                                    'name_marking' => ucwords(strtolower(trim($row[6]))),
                                    'initials' => strtoupper(trim($row[7])),
                                    'gender' => $row[8],
                                    'address1' => $row[9],
                                    'address2' => $row[10],
                                    'address3' => $row[11],
                                    'district' => $row[12],
                                    'medium' => $row[13],
                                    'mobile' => $row[14],
                                    'phone1' => $row[15],
                                    'phone2' => $row[16],
                                    'email' => strtolower(trim($row[17])),
                                    'al_index_no' => $row[18],
                                    'zscore' => $row[19],
                                    'batch' => $this->batch,
                                    'regulation_id'=>$this->regulation_id]
                                );
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
        }
    }
}
