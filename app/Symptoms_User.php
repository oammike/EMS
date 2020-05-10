<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Symptoms_User extends Model
{
    protected $table='symptoms_user';

    protected $fillable = [
        'user_id','question_id'
    ];
}
