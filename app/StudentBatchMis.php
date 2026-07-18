<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentBatchMis extends Model
{
    protected $table = 'student_batch_mis';

	protected $fillable = ['student_id', 'reason', 'old_regulation', 'old_batch'];

    public $timestamps = false;
    

}
