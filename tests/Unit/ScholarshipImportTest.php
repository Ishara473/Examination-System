<?php

namespace Tests\Unit;

use App\Imports\ScholarshipImport;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ScholarshipImportTest extends TestCase
{
    public function test_it_throws_when_required_columns_are_missing()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The uploaded file is missing required columns. Please ensure it contains both "Registration Number" and "Awarded Date".');

        $import = new ScholarshipImport(1);
        $import->collection(new Collection([
            ['AG/2020/001'],
        ]));
    }

    public function test_it_throws_when_date_format_is_invalid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid date format in the Awarded Date column. Please use ISO format (YYYY-MM-DD).');

        $import = new ScholarshipImport(1);
        $import->collection(new Collection([
            ['Registration Number', 'Awarded Date'],
            ['AG/2020/001', '20/03/2024'],
        ]));
    }

    public function test_it_throws_when_excel_date_object_is_used()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid date format in the Awarded Date column. Please use ISO format (YYYY-MM-DD).');

        $import = new ScholarshipImport(1);
        $import->collection(new Collection([
            ['Registration Number', 'Awarded Date'],
            ['AG/2020/001', new \DateTime('2024-03-20')],
        ]));
    }

    public function test_it_throws_when_excel_serial_date_is_used()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid date format in the Awarded Date column. Please use ISO format (YYYY-MM-DD).');

        $import = new ScholarshipImport(1);
        $import->collection(new Collection([
            ['Registration Number', 'Awarded Date'],
            ['AG/2020/001', 45388],
        ]));
    }
}
