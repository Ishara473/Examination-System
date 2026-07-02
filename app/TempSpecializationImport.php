<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempSpecializationImport extends Model
{
    protected $table = 'temp_specialization_upload';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id', 'specialization', 'specialization_id' ];

}
