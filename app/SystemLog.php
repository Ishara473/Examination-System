<?php

namespace App;

use Carbon\Carbon;


use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';
	
	public $timestamps = false;

    protected $fillable = [ 'ip', 'user_id', 'time','module','description' ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function setTimeAttribute($date){
        return $this->attributes['time'] = Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    public function getTimeAttribute($date){
        return (empty($date)|| $date =='0000-00-00 00:00:00' ||$date =='')? null: Carbon::parse($date)->format('Y-m-d H:i:s');
    }

}
