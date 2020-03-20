<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_SLearnings extends Model
{
   protected $table = 'user_slearnings';
   protected $fillable = [
        'user_id','slupdate_id'
    ];
}
