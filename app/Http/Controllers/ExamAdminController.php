<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }
    
}
