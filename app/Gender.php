<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'master_gender_types';

	protected $fillable = ['name'];

	public $timestamps = false;
}
