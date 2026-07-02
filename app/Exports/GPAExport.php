<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GPAExport implements FromView
{
    private $data;
    private $subjects;
    private $grades;

    function __construct($data,$subjects,$grades) {
        $this->data = $data;
        $this->subjects = $subjects;
        $this->grades = $grades;
    }

    public function view() : View
    {
        ini_set('max_execution_time', 600);

        return view('admin.results.excel.gpa-export',['data'=>$this->data,'subjects'=>$this->subjects,'grades'=>$this->grades]);

    }
}
