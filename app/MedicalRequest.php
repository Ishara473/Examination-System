<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalRequest extends Model
{
    protected $table = 'student_medicals';
	
    public $timestamps = false;
    
    protected $casts = [
        'requested_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

	protected $fillable = ['academic_year', 'requested_date', 'student_id', 'start_date', 'end_date', 'subject_id', 'component_type', 'is_issued_by_mo', 'remarks', 'status' ];

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
	}
}
