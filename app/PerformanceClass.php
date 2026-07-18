<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerformanceClass extends Model
{
    protected $table = 'master_performance_class';

	protected $fillable = ['class', 'upper_limit', 'lower_limit'];

	public $timestamps = false;
}

