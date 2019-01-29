<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Survey_User extends Model
{
    protected $table='survey_user';

    protected $fillable = [
        'survey_id','user_id','isDraft','startDate','done','lastItem'
    ];
}
