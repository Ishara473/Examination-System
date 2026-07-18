<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAL extends Model
{
    protected $table = 'student_al_results';

	protected $fillable = ['student_id', 'year', 'index_no', 'attempt','zscore', 'type', 'subject1', 'result1', 'subject2', 'result2', 'subject3', 'result3', 'subject4', 'result4', 'subject5', 'result5', 'subject6', 'result6' ];

	public $timestamps = false;

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
	}
}
