<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Symptoms_Declaration extends Model
{
    protected $table='symptoms_declare';

    protected $fillable = [
        'user_id','symptoms_id', 'user_answerID','isDiagnosis'
    ];
}
