<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_CCTV extends Model
{
    protected $table = "user_cctv";
    protected $fillable = [
        'user_id', 'logType'
        
    ];
}
