<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use App\TempStudentImport;

use Carbon\Carbon;
use Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements ToCollection,  WithMultipleSheets
{
    private $batch;
    private $regulation_id ;


    function __construct($batch,$regulation_id) 
    {
        $this->batch = $batch;
        $this->regulation_id = $regulation_id;
    }

    private function parseRegistrationDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $ex) {
                // fallback to text parse below
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $ex) {
            return null;
        }
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

        foreach ($rows as $key => $row) {
            $registration_no = strtoupper(trim($row[0] ?? ''));
            if ($registration_no === '') {
                continue;
            }

            try {
                TempStudentImport::create([
                    'registration_no' => $registration_no,
                    'status' => $row[1] ?? null,
                    'registration_date' => $this->parseRegistrationDate($row[2] ?? null),
                    'nic' => strtoupper(trim($row[3] ?? '')),
                    'full_name' => ucwords(strtolower(trim($row[4] ?? ''))),
                    'title' => $row[5] ?? null,
                    'name_marking' => ucwords(strtolower(trim($row[6] ?? ''))),
                    'initials' => strtoupper(trim($row[7] ?? '')),
                    'gender' => $row[8] ?? null,
                    'address1' => $row[9] ?? null,
                    'address2' => $row[10] ?? null,
                    'address3' => $row[11] ?? null,
                    'district' => $row[12] ?? null,
                    'medium' => $row[13] ?? null,
                    'mobile' => $row[14] ?? null,
                    'phone1' => $row[15] ?? null,
                    'phone2' => $row[16] ?? null,
                    'email' => strtolower(trim($row[17] ?? '')),
                    'al_index_no' => $row[18] ?? null,
                    'zscore' => $row[19] ?? null,
                    'batch' => $this->batch,
                    'regulation_id' => $this->regulation_id,
                ]);
            } catch (\Exception $ex) {
                Log::notice('StudentImport failed on row '.$key.': '.$ex->getMessage());
                continue;
            }
        }
    }
}
