<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Rowtable extends Model
{
     protected $table= 'rowtable';

    protected $fillable = [
        'name','description'
    ];
}
