<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Columntable extends Model
{
    protected $table= 'columntable';

    protected $fillable = [
        'name','description'
    ];
}
