<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentContact extends Model
{
	protected $table = 'student_contact_details';
	
	public $timestamps = false;

	protected $fillable = ['student_id', 'email', 'phone', 'mobile', 'address1', 'address2', 'address3', 'district', 'gn_division', 'electorate', 'moh_area','contact_address1', 'contact_address2', 'contact_address3', 'country' ];

	public function Student(){
		return $this->belongsTo('\App\Student', 'student_id', 'id');
	}
}
