<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSubject extends Model
{
    protected $table = 'course_subjects';

	protected $fillable = ['code', 'name', 'regulation_id', 'year', 'semester', 'status', 'type', 'credits', 'amount', 'surcharge'];

	public $timestamps = false;


	public function Specialization(){
		return $this->belongsToMany('App\Specialization', 'course_subject_specialization', 'subject_id', 'specialization_id');
	}

	public function Lectueres(){
        return $this->belongsToMany('App\User','user_subject', 'couse_subject_id','user_id');
    }
}
