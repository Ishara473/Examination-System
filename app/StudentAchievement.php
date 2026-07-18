<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    protected $table = 'student_achievements';

	protected $fillable = ['student_id', 'type', 'comment'];

    public $timestamps = false;
}
