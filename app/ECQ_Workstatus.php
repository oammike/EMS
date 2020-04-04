<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class ECQ_Workstatus extends Model
{
    protected $table= 'eqc_workstatus';

    protected $fillable = [
        'user_id','biometrics_id','workStatus'
    ];
}
