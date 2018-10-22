<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Biometrics extends Model
{
    protected $table= 'biometrics';

    protected $fillable = [
        'productionDate', //'user_id'
    ];
}
