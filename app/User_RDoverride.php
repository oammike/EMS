<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_RDoverride extends Model
{
    protected $table= 'user_RDoverride';

    protected $fillable = [
        'user_id','biometrics_id'

    ];
}
