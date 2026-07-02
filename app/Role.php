<?php

namespace App;

use Spatie\Permission\Models\Role as SpartieRole;

class Role extends SpartieRole
{

    protected $fillable = [
        'guard_name', 'name','description'
    ];


}
