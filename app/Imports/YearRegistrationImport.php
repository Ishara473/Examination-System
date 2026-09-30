<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\TempYearRegistrationImport;
use Log;
use Exception;


class YearRegistrationImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        // Validate that the file has a proper header row
        if ($rows->count() > 0) {
            $headers = $rows[0]; // First row should be headers
            $requiredColumns = ['registration_no', 'year', 'study_year', 'paid_amount', 'hostel'];
            
            // Check if first row looks like headers or data
            $isHeaderRow = true;
            $headerCheckCount = 0;
            
            // Normalize headers to lowercase for comparison
            for ($i = 0; $i < count($requiredColumns); $i++) {
                if (!isset($headers[$i])) {
                    throw new Exception("File format error: Missing columns. Expected header row with columns: " . implode(", ", $requiredColumns));
                }
                
                $headerValue = strtolower(trim((string)$headers[$i]));
                
                // Check if this value matches expected header
                if ($headerValue === $requiredColumns[$i]) {
                    $headerCheckCount++;
                } else {
                    $isHeaderRow = false;
                }
            }
            
            // If less than 3 columns matched, likely not a header row
            if ($headerCheckCount < 3) {
                throw new Exception("File format error: Missing or incorrect column headers. First row should contain: " . implode(", ", $requiredColumns) . ". File appears to have data without headers.");
            }
            
            // If we have header mismatches, show which column is wrong
            if (!$isHeaderRow) {
                for ($i = 0; $i < count($requiredColumns); $i++) {
                    $headerValue = strtolower(trim((string)$headers[$i]));
                    if ($headerValue !== $requiredColumns[$i]) {
                        throw new Exception("Column header mismatch at position " . ($i+1) . ": expected '{$requiredColumns[$i]}' but found '{$headers[$i]}'");
                    }
                }
            }
            
            // Skip the header row when processing data
            unset($rows[0]);
        }
        
        try{
            foreach ($rows as $key=>$row){
                $registration_no = strtoupper(trim($row[0]));
                if($registration_no!= ''){
                    // Validate all required fields have values
                    $rowNum = $key + 1; // Row number for error message
                    
                    // Check registration_no is not empty
                    if (empty($registration_no)) {
                        throw new Exception("Row $rowNum: 'registration_no' is required but empty");
                    }
                    
                    // Check year is not empty and numeric
                    $year = isset($row[1]) ? trim($row[1]) : '';
                    if (empty($year)) {
                        throw new Exception("Row $rowNum: 'year' is required but empty");
                    }
                    if (!is_numeric($year)) {
                        throw new Exception("Row $rowNum: 'year' must be numeric, got '$year'");
                    }
                    
                    // Check study_year is not empty and numeric
                    $study_year = isset($row[2]) ? trim($row[2]) : '';
                    if (empty($study_year)) {
                        throw new Exception("Row $rowNum: 'study_year' is required but empty");
                    }
                    if (!is_numeric($study_year)) {
                        throw new Exception("Row $rowNum: 'study_year' must be numeric, got '$study_year'");
                    }
                    
                    // Check paid_amount is not empty and numeric
                    $paid_amount = isset($row[3]) ? trim($row[3]) : '';
                    if (empty($paid_amount)) {
                        throw new Exception("Row $rowNum: 'paid_amount' is required but empty");
                    }
                    if (!is_numeric($paid_amount)) {
                        throw new Exception("Row $rowNum: 'paid_amount' must be numeric, got '$paid_amount'");
                    }
                    
                    // Check hostel is not empty and is Y or N
                    $hostel = isset($row[4]) ? strtoupper(trim($row[4])) : '';
                    if (empty($hostel)) {
                        throw new Exception("Row $rowNum: 'hostel' is required but empty. Use 'Y' or 'N'");
                    }
                    if ($hostel !== 'Y' && $hostel !== 'N') {
                        throw new Exception("Row $rowNum: 'hostel' must be 'Y' or 'N', got '$hostel'");
                    }
                    
                    $data = TempYearRegistrationImport::create([
                        'registration_no' => $registration_no,
                        'year'=> $year,
                        'study_year' => $study_year,
                        'paid_amount' => $paid_amount,
                        'hostel'=> ($hostel == 'Y') ? 1 : 0,

                    ]);
                }
            }
        }catch(\Exception $ex){
            Log::notice($ex->getMessage());
            throw $ex;
        }
    }
}
