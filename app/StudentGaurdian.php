<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class StudentGaurdian extends Model
{
	protected $table = 'student_gaurdian';
	
	public $timestamps = false;

	protected $fillable = ['student_id', 'type', 'full_name', 'occupation', 'address', 'phone', 'emergency_c_name', 'emergency_c_phone'];

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
	}

		
    
}
