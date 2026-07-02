<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class StudentAccDetail extends Model
{
    protected $table = 'student_academic_details';

	protected $fillable = ['student_id', 'batch', 'regulation_id', 'registration_date', 'registration_status', 'current_study_year', 'status', 'specialization_id', 'is_hostal', 'is_batch_miss', 'is_complete', 's1_gpa', 's2_gpa', 's3_gpa', 's4_gpa', 's5_gpa', 's6_gpa','s7_gpa', 's8_gpa', 'y2_gpa', 'final_gpa', 'class', 'degree_effective_date', 'main_scholarship', 'other_scholarship','scholarship_start_date'];

	public $timestamps = false;

	protected $casts = [
		'registration_date' => 'date',
		'scholarship_start_date' => 'date',
	];

	/**
     * Date Set Format
     *
     */
    public function setRegistrationDateAttribute($date){
        return $this->attributes['registration_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function setScholarshipStartDateAttribute($date){
        return $this->attributes['scholarship_start_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function setDegreeEffectiveDateAttribute($date){
        return $this->attributes['degree_effective_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * Date Get Format
     *
     */
    public function getRegistrationDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d');
    }
    
    public function getScholarshipStartDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d');
    }
    
    public function getDegreeEffectiveDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d');
    }
    
    /**Relationships */	

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
    }
    
    public function Specialization(){
        return $this->hasOne('\App\Specialization', 'id', 'specialization_id');
    }

    public function BatchCode(){
        $batch = Batch::where('id','=',$this->batch)->first();
        return $batch ? $batch->code : null;
    }

    public function Status(){
        $status = [1=>'Enrolled',2=>'Graduated',-1=>'Dropped'];
        return $status[$this->status];
    }
}
