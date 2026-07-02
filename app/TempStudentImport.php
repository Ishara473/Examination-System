<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;


class TempStudentImport extends Model
{
    protected $table = 'temp_student_upload_rusl_file';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'status', 'registration_date', 'nic', 'full_name', 'title', 'name_marking', 'initials', 'gender', 'address1', 'address2', 'address3', 'district', 'medium', 'mobile', 'phone1', 'phone2', 'email', 'al_index_no', 'zscore', 'batch', 'regulation_id'];

    protected $casts = [
        'registration_date' => 'date',
    ];

    public function setRegistrationDateAttribute($date){
        return $this->attributes['registration_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function getRegistrationDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00 00:00:00' ||$date =='')? null: Carbon::parse($date)->format('m/d/Y');
    }
}
