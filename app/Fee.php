<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'master_fee';

	protected $fillable = ['code', 'name', 'amount', 'surcharge_amount'];

	public $timestamps = false;
}
