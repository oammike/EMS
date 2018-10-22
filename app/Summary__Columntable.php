<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Summary__Columntable extends Model
{
    protected $table= 'summary__Columntable';

    protected $fillable = [
        'summary_id','columntable_id'
    ];
}
