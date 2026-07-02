<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExamResult extends Model
{
    protected $table = 'student_exam_results';
	
	public $timestamps = false;

    protected $fillable = [ 'student_id', 'year', 'course_subject_id', 'marks', 'result', 'status' ];

}
