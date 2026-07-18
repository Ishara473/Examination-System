<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempTranferredList extends Model
{
    protected $table = 'temp_transferred_list';
	
	public $timestamps = false;

    protected $fillable = [ 'registration_no', 'student_id' ];

}
