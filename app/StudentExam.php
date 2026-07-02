<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    protected $table = 'student_exam';

	protected $fillable = ['year', 'semester', 'student_id', 'status' ];

    public $timestamps = false;
    
    public function Subjects(){
        return $this->hasMany('\App\StudentExamSubjects', 'student_exam_id', 'id');
    }
}
