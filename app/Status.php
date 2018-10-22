<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
   protected $table = "statuses";
    protected $fillable = [
        'name','orderNum'
    ];

   

}
