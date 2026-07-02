<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SpecializationExport implements FromView
{
    private $data;

    function __construct($data) {
        $this->data = $data;
    }

    public function view() : View
    {
        return view('admin.registration.excel.specialization',['data'=>$this->data]);
    }
}
