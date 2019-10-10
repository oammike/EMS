<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table='gallery';

    protected $fillable = [
        'name','description','albumDate','canContribute'
    ];
}
