<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YearRegistration extends Model
{
    protected $table = 'student_yearly_registration';
	
	public $timestamps = false;

	protected $fillable = ['student_id', 'academic_year', 'registered_year', 'registration_date', 'need_hostel', 'total_paid_amount', 'total_year_credits', 'status' ];

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
    }
    
    public function Payments(){
        return $this->hasMany('\App\YearRegistrationPayment', 'yearly_registration_id', 'id');
    }

    public function Subjects(){
        return $this->hasMany('\App\YearRegistrationSubject', 'yearly_registration_id', 'id');
    }
}
