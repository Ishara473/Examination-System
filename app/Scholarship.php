<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $table = 'master_scholarship_types';

    protected $fillable = [ 'name'];

    public $timestamps = false;
}
