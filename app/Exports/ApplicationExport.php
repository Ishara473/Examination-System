<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApplicationExport implements FromView
{
    private $data;
    private $subjects;

    function __construct($data,$subjects) {
        $this->data = $data;
        $this->subjects = $subjects;
    }

    public function view() : View
    {
        return view('admin.exam.excel.application',['data'=>$this->data,'subjects'=>$this->subjects]);
    }
}
