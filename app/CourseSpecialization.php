<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSpecialization extends Model
{
    protected $table = 'course_subject_specialization';

	protected $fillable = [ 'subject_id', 'specialization_id','type'];

	public $timestamps = false;

	public $incrementing = false;

}
