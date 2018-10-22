<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Summary__Rowtable extends Model
{
    protected $table= 'summary__Rowtable';

    protected $fillable = [
        'summary_id','rowtable_id'
    ];
}
