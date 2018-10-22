<?php

namespace OAMPI-Eval;

use Illuminate\Database\Eloquent\Model;

class Competency__Attribute__EvalSetting extends Model
{
    protected $table= 'competency__Att__EvalSetting';

    protected $fillable = [
        'competency__Attribute_id','evalSetting_id'
    ];
}
