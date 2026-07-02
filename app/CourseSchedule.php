<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    protected $table = 'course_schedules';

	protected $fillable = ['code', 'raw_code','name', 'start_date', 'overdue_date', 'end_date', 'is_enabled', 'counter' ];

	public $timestamps = false;

}
