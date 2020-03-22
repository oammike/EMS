<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_LogOverride extends Model
{
    protected $table = 'user_logOverride';
    protected $fillable = [
        'user_id','productionDate','logTime','affectedBio','logType_id'
    ];
}
