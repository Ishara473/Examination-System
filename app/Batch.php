<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = 'master_batch';

	protected $fillable = ['batch', 'al_year', 'academic_year', 'is_current','program_id'];

	public $timestamps = false;

	public function Program(){
		return $this->belongsTo('\App\Program', 'program_id', 'id');
	}

}
