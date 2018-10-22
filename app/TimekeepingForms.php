<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class TimekeepingForms extends Model
{
    protected $table = "timekeepingForms";
    protected $fillable = [
        'name','code'
       
    ];
}
