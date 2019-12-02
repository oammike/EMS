<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_SpecialAccess extends Model
{
    protected $table = 'user_specialAccess';
    protected $fillable = [
        'startDate','endDate', 'user_id','role_id'
    ];
}
