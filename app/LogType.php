<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class LogType extends Model
{
    protected $table= 'logType';

    protected $fillable = [
        'name','code' //'user_id'
    ];
}
