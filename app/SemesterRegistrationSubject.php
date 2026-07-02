<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SemesterRegistrationSubject extends Model
{
    protected $table = 'student_semester_registration_subjects';
	
	public $timestamps = false;

	protected $fillable = ['student_id', 'subject_id', 'type', 'status', 'remarks' ];


}
