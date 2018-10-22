<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = "schedules";
    protected $fillable = [
        'user_id', 'workday', 'timeStart','timeEnd','isFlexi'
        
    ];

    
}
