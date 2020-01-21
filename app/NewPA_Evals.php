<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class NewPA_Evals extends Model
{
    protected $table= 'newPA_evals';
    protected $fillable = [
        'user_id', 'evaluatedBy','finalRating','form_id','startPeriod','endPeriod','isScreened','isNoted'
    ];
}
