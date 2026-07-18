<?php

namespace App;

use Spatie\Permission\Models\Permission as SpartiePermission;

class Permission extends SpartiePermission
{
    protected $fillable = ['guard_name', 'name','description' ];
}
