<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecializationRequest extends Model
{
    protected $table = 'student_spcialization_requests';
	
	public $timestamps = false;

	protected $fillable = ['year', 'student_id', 'specialization_id', 'preference_order', 'status' ];

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
	}
}
