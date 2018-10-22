<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class UserType_Roles extends Model
{
    protected $table= 'userType_roles';

    protected $fillable = [
        'userType_id','role_id'
    ];
}
