<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VLEAccountExport implements FromView
{
    private $data;
    private $maxSubCount;

    function __construct($data,$maxSubCount) { //,$subjects
        $this->data = $data;
        $this->maxSubCount = $maxSubCount;
    }

    public function view() : View
    {
        return view('admin.registration.excel.lms-accounts',['data'=>$this->data,'maxSubCount'=>$this->maxSubCount]);
    }
}
