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

    private function normalizeHeader($header)
    {
        if ($header === null) {
            return '';
        }

        $normalized = strtolower(trim((string) $header));
        $normalized = preg_replace('/[^a-z0-9]+/', '', $normalized);

        return $normalized;
    }

    private function normalizeCellValue($value)
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return trim((string) $value);
    }

    private function normalizeDateValue($dateValue)
    {
        if ($dateValue === null || $dateValue === '') {
            return null;
        }

        if (is_string($dateValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
            return $dateValue;
        }

        throw new \Exception('Invalid date format in the Awarded Date column. Please use ISO format (YYYY-MM-DD).');
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception('The uploaded file is empty.');
        }

        $headers = $rows->first();
        $normalizedHeaders = [];
        foreach ($headers as $header) {
            $normalizedHeaders[] = $this->normalizeHeader($header);
        }

        $hasRegistrationColumn = collect($normalizedHeaders)->contains(function ($header) {
            return $header === 'registrationnumber' || $header === 'registrationno';
        });
        $hasAwardedDateColumn = collect($normalizedHeaders)->contains(function ($header) {
            return $header === 'awardeddate' || $header === 'date';
        });

        if (!$hasRegistrationColumn || !$hasAwardedDateColumn) {
            throw new \Exception('The uploaded file is missing required columns. Please ensure it contains both "Registration Number" and "Awarded Date".');
        }

        $dataRows = $rows->slice(1);

        // Find the first row with data to check column order
        $firstDataRow = null;
        foreach ($dataRows as $row) {
            if (isset($row[0]) && trim($row[0]) !== '') {
                $firstDataRow = $row;
                break;
            }
        }

        if ($firstDataRow) {
            $col0 = $this->normalizeCellValue($firstDataRow[0] ?? '');
            $col1 = $this->normalizeCellValue($firstDataRow[1] ?? '');

            $col0IsDate = is_numeric($col0) || preg_match('/^\d+[-\/\.]\d+[-\/\.]\d+/', $col0);
            $col1IsReg = preg_match('/^[A-Z]+[\/\-]\d+/i', $col1);
            $col0IsReg = preg_match('/^[A-Z]+[\/\-]\d+/i', $col0);

            if (($col0IsDate && $col1IsReg) || (!$col0IsReg && $col1IsReg)) {
                throw new \Exception("Incorrect column order detected. The first column must be the Registration Number (e.g., AG/2020/002) and the second column must be the Awarded Date.");
            }
        }

        foreach ($dataRows as $row){
            $registrationValue = $row[0] ?? null;
            $registration_no = is_string($registrationValue) ? strtoupper(trim($registrationValue)) : '';
            if ($registration_no === '') {
                continue;
            }
            $dateValue = $row[1] ?? null;
            Log::notice('ScholarshipImport dateValue: ' . (is_object($dateValue) ? get_class($dateValue) . ' ' . print_r($dateValue, true) : var_export($dateValue, true)));
            $date = $this->normalizeDateValue($dateValue);
            if ($date === null) {
                continue;
            }
            TempScholarshipImport::create([
                'registration_no' => $registration_no,
                'student_id'=>0,
                'awarded_date' => $date,
                'scholarship_type' => $this->scholaship_type
            ]);
        }
    }
}
