<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_Question_Category extends Model
{
    protected $table='survey_question_category';

    protected $fillable = [
        'survey_questionID','categoryTag_id'
    ];
}
