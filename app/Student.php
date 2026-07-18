<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Student extends Authenticatable
{
    use Notifiable;

    // use SoftDeletes;
    
    protected $guard = 'student';

    protected $table = 'student_personal_details';

    protected $casts = [
        'dob' => 'date',
        'last_login' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    /**
     * The attributes that are mass assignable.
     *
     */
    protected $fillable = [
        'registration_no', 'index_no', 'year', 'full_name','full_name_sinhala','full_name_tamil','name_marking','initials','id_type','gender','id_no','id_no_2', 'dob','citizenship','citizenship_type', 'nationality','race','religion','medium', 'civil_status','writing_hand', 'status','is_profile_confirmed', 'last_login', 'hash_key',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * Date Set Format
     *
     */
    public function setDOBAttribute($date){
        return $this->attributes['dob'] = Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * Date Get Format
     *
     */
    public function getDOBAttribute($date){
        return (empty($date)|| $date =='0000-00-00 00:00:00' ||$date =='')? NULL: Carbon::parse($date)->format('Y-m-d');
    }


    /**
     * Relationship
     * 
     */
    public function Gaurdian(){
        return $this->hasOne('\App\StudentGaurdian', 'student_id', 'id');
    }

    public function Contact(){
        return $this->hasOne('\App\StudentContact', 'student_id', 'id');
    }

    public function AL(){
        return $this->hasOne('\App\StudentAL', 'student_id', 'id');
    }

    public function AcademicDetail(){
        return $this->hasOne('\App\StudentAccDetail', 'student_id', 'id');
    }

    public function SpecializationRequests(){
        return $this->hasMany('\App\SpecializationRequest', 'student_id', 'id');
    }

    public function Medicals(){
        return $this->hasMany('\App\MedicalRequest', 'student_id', 'id');
    }

    public function YearRegistrations(){
        return $this->hasMany('\App\YearRegistration', 'student_id', 'id');
    }


    public function Results(){
        return $this->hasMany('\App\StudentExamResult', 'student_id', 'id');
    }

    public function GenderName(){
        return Gender::where('id','=',$this->gender)->first()->name;
    }


}
