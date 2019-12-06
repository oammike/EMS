<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table='task';

    protected $fillable = [
        'id','name','groupID','campaign_id'
    ];
}
