<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Options extends Model
{
    protected $table='survey_options';

    protected $fillable = [
        'survey_id','options_id'
    ];
}
