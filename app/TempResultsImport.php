<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempResultsImport extends Model
{
    protected $table = 'temp_exam_results';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id', 'year', 'subject_code', 'course_subject_id', 'marks', 'result', 'uploaded_by' ];

}
