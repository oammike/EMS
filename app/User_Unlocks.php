<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Unlocks extends Model
{
    protected $table = 'user_unlocks';
    protected $fillable = [
         'user_id','productionDate'
    ];
}
