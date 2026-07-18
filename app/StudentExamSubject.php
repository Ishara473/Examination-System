<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExamSubject extends Model
{
    protected $table = 'student_exam_subjects';
	
	public $timestamps = false;

	protected $fillable = ['student_exam_id', 'subject_id', 'exam_type', 'is_repeat', 'status','registered' ];

	public function StudentExam(){
		return $this->belongsTo('\App\StudentExam', 'student_exam_id', 'id');
	}
}
