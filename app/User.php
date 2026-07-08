<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Spatie\Permission\Traits\HasRoles;
// use Spatie\Permission\Traits\HasPermissions;

class User extends Authenticatable
{
    use Notifiable;

    use HasRoles;

    // use HasPermissions;

    protected $guard = 'users';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'last_login', 'first_name', 'last_name', 'designation', 'is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Legacy users.permissions column conflicts with Spatie's permissions relationship.
     */
    public function getAttribute($key)
    {
        if ($key === 'permissions') {
            if (! $this->relationLoaded('permissions')) {
                $this->load('permissions');
            }

            return $this->getRelation('permissions');
        }

        return parent::getAttribute($key);
    }

    public function Subjects(){
        return $this->belongsToMany('\App\CourseSubject','user_subject','user_id','couse_subject_id');
    }

    public static function searchUsers($search)
    {
        $query = self::query()->select('id as ID', 'first_name as FirstName', 'last_name as LastName', 'email as Email', 'designation as Designation');

        if (is_string($search) && strlen(trim($search)) > 2) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                  ->orWhere('last_name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('designation', 'like', $term);
            });
        }

        return $query;
    }

    // public function hasRole($slug){
    //     if($this->roles()->where('slug','=',$slug)->first())return true;
    //     else return false;
    // }
}
