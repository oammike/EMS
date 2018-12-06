<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class SLupdate extends Model
{
    
    protected $table = 'slupdate';
    protected $fillable = [
        'period','credits'
    ];


    
}
