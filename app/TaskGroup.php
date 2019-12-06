<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    protected $table='taskgroup';

    protected $fillable = [
        'name'
    ];
}
