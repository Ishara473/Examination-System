<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class TempScholarshipImport extends Model
{
    protected $table = 'temp_scholarship_upload';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id', 'awarded_date', 'scholarship_type' ];

    public function setAwardedDateAttribute($date){
        return $this->attributes['awarded_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function getAwardedDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d');
    }

}
