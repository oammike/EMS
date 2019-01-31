<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Notes extends Model
{
    protected $table='survey_notes';

    protected $fillable = [
        'user_id','question_id','comments'
    ];
}
