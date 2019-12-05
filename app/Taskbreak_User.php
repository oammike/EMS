<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Taskbreak_User extends Model
{
    protected $table='taskbreak_user';

    protected $fillable = [
        'id','user_taskID','timeStart','timeEnd'
    ];
}
