<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Response extends Model
{
    protected $table='survey_response';

    protected $fillable = [
        'user_id','question_id','survey_optionID'
    ];
}
