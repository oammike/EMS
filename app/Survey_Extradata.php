<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Extradata extends Model
{
    protected $table='survey_extradata';

    protected $fillable = [
        'user_id','survey_id','gender','education','course','currentLocation','commuteTime','hobbiesInterest'
    ];
}
