<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseGrade extends Model
{
    protected $table = 'master_grade';

	protected $fillable = ['grade', 'grade_point', 'upper_mark_limit', 'lower_mark_limit'  ];

	public $timestamps = false;

}
