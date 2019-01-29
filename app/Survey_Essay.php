<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Essay extends Model
{
    protected $table='survey_essay';

    protected $fillable = [
        'survey_id','user_id','question_id','answer'
    ];
}
