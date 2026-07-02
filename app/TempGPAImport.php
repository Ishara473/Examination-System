<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempGPAImport extends Model
{
    protected $table = 'temp_gpa_upload';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id', 'gpa' ];
}
