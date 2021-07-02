<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Intro extends Model
{
    protected $table='survey_intro';

    protected $fillable = [
        'survey_id','body'
    ];
}
