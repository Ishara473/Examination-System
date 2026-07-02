<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class StudentExport implements FromView
{
    private $data;
    private $specilization;

    function __construct($data,$specilization) {
        $this->data = $data;
        $this->specilization = $specilization;
    }

    public function view() : View
    {
        return view('admin.students.excel.student-list',['data'=>$this->data,'specilization'=>$this->specilization]);
    }
}
