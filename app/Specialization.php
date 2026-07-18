<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    
    protected $table = 'master_course_specialization_categories';
	
	public $timestamps = false;

	protected $fillable = ['department','name' ];

}
