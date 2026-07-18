<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    protected $table = 'master_regulation';

    protected $fillable = [ 'name', 'by_law_version', 'version', 'is_current'];

    public $timestamps = false;
}
