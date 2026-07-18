<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YearRegistrationPayment extends Model
{
    protected $table = 'student_yearly_registration_payments';
	
	public $timestamps = false;

	protected $fillable = [ 'yearly_registration_id', 'payment_id', 'amount', 'remarks' ,'is_late_pay'];

	public function YearRegistration(){
		return $this->belongsTo('\App\YearRegistration', 'yearly_registration_id', 'id');
	}
	
}
