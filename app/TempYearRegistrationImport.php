<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempYearRegistrationImport extends Model
{
    protected $table = 'temp_year_registration_upload';
    
    public $timestamps = false;

    protected $fillable = [ 'student_id', 'registration_no', 'year', 'study_year', 'paid_amount', 'hostel'];
    
}
