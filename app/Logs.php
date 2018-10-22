<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table= 'logs';

    protected $fillable = [
        'user_id','logTime','logType_id','biometrics_id' //'user_id'
    ];
}
