<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    protected $table = "leaves";
    protected $fillable = [
        'name','code'
       
    ];
}
