<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'userType';
    protected $fillable = [
        'name','description'
    ];


    public function roles()
    {
    	return $this->belongsToMany(Role::class, 'userType_roles', 'userType_id', 'role_id');

    }
}
