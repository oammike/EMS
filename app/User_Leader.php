<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Leader extends Model
{
     protected $table = "user_leaders";
    protected $fillable = [
         'user_id','immediateHead_Campaigns_id'
    ];
}
