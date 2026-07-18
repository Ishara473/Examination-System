<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ALSubject extends Model
{
    protected $table = 'master_al_subjects';

	protected $fillable = ['subject'];

	public $timestamps = false;
}
