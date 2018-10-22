<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $table= 'competency';

    protected $fillable = [
        'name','percentage','agentPercentage', 'definitions', 'acrossTheBoard'
    ];
}
