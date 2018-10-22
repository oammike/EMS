<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class EvalType extends Model
{
    protected $table= 'evalType';

    protected $fillable = [
        'name'
    ];
}
