<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class TempGraduateList extends Model
{
    protected $table = 'temp_graduate_list';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id', 'degree_effective_date' ];

    protected $casts = [
        'degree_effective_date' => 'date',
    ];


    public function setDegreeEffectiveDateAttribute($date){
        return $this->attributes['degree_effective_date'] = Carbon::parse($date)->format('Y-m-d');
    }

    public function getDegreeEffectiveDateAttribute($date){
        return (empty($date)|| $date =='0000-00-00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d');
    }

}
