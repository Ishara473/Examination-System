<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'master_degree_programs';

    protected $fillable = [ 'name', 'short_name','special_name'];

    public $timestamps = false;
}
