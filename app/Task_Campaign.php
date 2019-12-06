<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Task_Campaign extends Model
{
    protected $table='task_campaign';

    protected $fillable = [
        'campaign_id','name'
    ];
}
