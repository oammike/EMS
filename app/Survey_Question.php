<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Question extends Model
{
    protected $table='survey_questions';

    protected $fillable = [
        'value','responseType','order'
    ];
}
