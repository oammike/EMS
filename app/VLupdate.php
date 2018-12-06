<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class VLupdate extends Model
{
    
    protected $table = 'vlupdate';
    protected $fillable = [
        'period','credits'
    ];


    
}
